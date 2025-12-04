<?php

namespace App\Http\Controllers;

use App\Helpers\HubspotClientHelper;
use App\Imports\HubspotDeals;
use App\Imports\HubspotForecastCategories;
use App\Imports\HubspotOwners;
use App\Imports\HubspotPipelines;
use App\Models\Organization;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Hash;
use Laravel\Socialite\Facades\Socialite;

class HubspotController extends Controller
{
    public function hubspotRedirect()
    {
        $parameters = [
            //'access_type' => 'offline',
            //'application_name' => config('app.name', ''),
            //'prompt' => "consent select_account" //ALWAYS ask for consent and returns refresh token
        ];

        $scopes = [
            'oauth',
            'crm.lists.read',
            'crm.objects.contacts.read',
            'crm.objects.companies.read',
            'crm.objects.deals.read',
            'crm.objects.owners.read',
            'crm.schemas.contacts.read',
            'crm.schemas.companies.read',
            'crm.schemas.deals.read',
            'crm.objects.deals.write',
        ];

        $redirectUri = config('services.hubspot.redirect');
        if (!$redirectUri) {
            $redirectUri = url('/hubspot/callback');
        }

        return Socialite::driver('hubspot')
            ->scopes($scopes)
            ->with(['redirect_uri' => $redirectUri])
            ->redirect();
    }

    public function hubspotCallback()
    {
        try {
            // Use stateless() to avoid InvalidStateException with OAuth
            $hubspotUser = Socialite::driver('hubspot')->stateless()->user();

            // All providers...
            //$googleUser->getId();
            //$googleUser->getNickname();
            //$googleUser->getName();
            //$googleUser->getEmail();
            //$googleUser->getAvatar();

            // OAuth 2.0 providers...
            //$token = $googleUser->token;
            //$refreshToken = $googleUser->refreshToken;
            //$expiresIn = $googleUser->expiresIn;
            //Retrieving User Details From A Token (OAuth2)
            //$user = Socialite::driver('github')->userFromToken($token);

            // OAuth 1.0 providers...
            //$token = $googleUser->token;
            //$tokenSecret = $googleUser->tokenSecret;
            //Retrieving User Details From A Token And Secret (OAuth1)
            //$user = Socialite::driver('twitter')->userFromTokenAndSecret($token, $secret);

            //hubspot
            //$hubspotUser->id
            //$hubspotUser->email
            //$hubspotUser->token
            //$hubspotUser->refreshToken
            //$hubspotUser->expires_in
            //$hubspotUser->user['hub_domain']

            $account_details = $this->account_details($hubspotUser->token);
            session(['hubspot_portalId' => $account_details->portalId]);

            $user = User::where('email', $hubspotUser->getEmail())->first();
            if (! $user) {
                $user = User::updateOrCreate([
                    'email' => $hubspotUser->getEmail(),
                ], [
                    'name' => $hubspotUser->getEmail(),
                    'hubspot_id' => $hubspotUser->getId(),
                    'password' => Hash::make($hubspotUser->getEmail().'@'.$hubspotUser->getId()),
                    'hubspot_token' => $hubspotUser->token,
                    'hubspot_refreshToken' => $hubspotUser->refreshToken,
                    'active' => true,
                    'role_id' => 2,
                ]);
            } else {
                $user->update([
                    'hubspot_id' => $hubspotUser->getId(),
                    'hubspot_token' => $hubspotUser->token,
                    'hubspot_refreshToken' => $hubspotUser->refreshToken,
                ]);
            }

            $organization = $user->organization();
            if (! $organization) {
                $organization = Organization::where('hubspot_portalId', $account_details->portalId)->first();
                if ($organization) {
                    //if(!$user->role_id) $user->role_id=2;
                    $user->organizations()->attach($organization);
                } else {
                    $var = explode('@', $user->email);
                    $domain_name = array_pop($var);
                    $domain_name = $domain_name.'_'.$account_details->portalId;
                    $organization = Organization::create([
                        'name' => $domain_name,
                        'currency' => 'EUR',
                        'hubspot_portalId' => $account_details->portalId,
                        'hubspot_uiDomain' => $account_details->uiDomain,
                        'timezone' => $account_details->timeZone,
                    ]);
                    $user->role_id = 3;
                    $user->organizations()->attach($organization);
                    $user->save();
                }
            }
            //$organization->update([
            //    'hubspot_portalId' => $account_details->portalId,
            //    'hubspot_uiDomain' => $account_details->uiDomain,
            //    'timezone' => $account_details->timeZone
            //]);

            Auth::loginUsingId($user->id);

            return redirect()->route('home');

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function sync_all()
    {
        if (config('app.env') == 'local') {
            //$time_start = microtime(true);//seconds

            $hubspot = HubspotClientHelper::createFactory(Auth::user());

            $organization = Auth::user()->organization();
            $organization->synchronizing = true;
            $organization->save();

            DB::beginTransaction();
            try {
                HubspotPipelines::sync_with_hubspot($hubspot, Auth::user(), $organization->id);
                HubspotForecastCategories::sync_with_hubspot($hubspot, Auth::user(), $organization->id);
                HubspotOwners::sync_with_hubspot($hubspot, Auth::user(), $organization->id);
                HubspotDeals::sync_with_hubspot($hubspot, Auth::user(), $organization->id);

                $organization->last_sync = Carbon::now();
                $organization->synchronizing = false;
                $organization->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $organization->synchronizing = false;
                $organization->save();
            }

            //$time_end = microtime(true);//seconds
            //ray($time_end - $time_start);
        } else {
            $organization = Auth::user()->organization();
            if (! $organization->isSynchronizing() && (($organization->getDayFromLastSync() == null) || ($organization->getDayFromLastSync() > 1))) {
                \App\Jobs\ImportHubspot::dispatch(Auth::user(), $organization->id);
                $organization->synchronizing = true;
                $organization->save();
            }
        }

        return redirect()->route('home');
    }

    public function account_details($token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.hubapi.com/account-info/v3/details',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer '.$token,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //echo "cURL Error #:" . $err;
            return [];
        } else {
            return json_decode($response);
        }

    }
}
