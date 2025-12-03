<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function googleRedirect()
    {
        $parameters = [
            'access_type' => 'offline',
            'approval_prompt' => 'force',
            'include_granted_scopes' => 'true',
            //'application_name' => config('app.name', ''),
            //'prompt' => "consent select_account" //ALWAYS ask for consent and returns refresh token
        ];

        $scopes =[
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/calendar',
            //'https://www.googleapis.com/auth/calendar.events'
            //'https://www.googleapis.com/auth/admin.directory.user.readonly',
            //'https://www.googleapis.com/auth/directory.readonly',
            //'https://www.google.com/m8/feeds/',
            //'https://www.googleapis.com/auth/contacts.readonly',
            //'https://www.googleapis.com/auth/spreadsheets'
        ];

        return Socialite::driver('google')
                        ->scopes($scopes)
                        ->with($parameters)
                        ->redirect();
    }

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = Auth::user();
            if ($user)
                $user->update([
                    //'google_email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_name' => $googleUser->getEmail(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);

            //$google_client_token = [
            //    'access_token' => $googleUser->token,
            //    'refresh_token' => $googleUser->refreshToken,
            //    'expires_in' => $googleUser->expiresIn
            //];
            //session(['google_client_token'=> $google_client_token]);

            return redirect()->route('user.show', Auth::user());

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
