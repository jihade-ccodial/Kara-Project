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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first to connect Google Calendar.');
        }

        $parameters = [
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
        ];

        $scopes =[
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/calendar',
        ];

        $state = base64_encode(json_encode(['user_id' => Auth::id()]));
        $parameters['state'] = $state;
        
        return Socialite::driver('google')
                        ->scopes($scopes)
                        ->with($parameters)
                        ->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            $userId = null;
            if ($request->has('state')) {
                $state = json_decode(base64_decode($request->get('state')), true);
                if (isset($state['user_id'])) {
                    $userId = $state['user_id'];
                }
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = null;
            if ($userId) {
                $user = User::find($userId);
            }
            
            if (!$user && Auth::check()) {
                $user = Auth::user();
            }
            
            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if (!$user) {
                    \Log::warning('Google OAuth callback: User not found', [
                        'google_email' => $googleUser->getEmail(),
                        'state_user_id' => $userId,
                    ]);
                    return redirect()->route('login')->with('error', 'User not found. Please login first.');
                }
                
                Auth::login($user);
            }

            $tokenData = [
                'access_token' => $googleUser->token,
                'expires_in' => $googleUser->expiresIn ?? 3600,
                'created' => time(),
            ];

            if ($googleUser->refreshToken) {
                $tokenData['refresh_token'] = $googleUser->refreshToken;
            }

            $updateData = [
                'google_id' => $googleUser->getId(),
                'google_name' => $googleUser->getEmail(),
                'google_token' => $tokenData,
            ];

            if ($googleUser->refreshToken) {
                $updateData['google_refresh_token'] = $googleUser->refreshToken;
            }

            $user->update($updateData);
            
            \Log::info('Google OAuth tokens saved', [
                'user_id' => $user->id,
                'email' => $user->email,
                'google_name' => $updateData['google_name'],
                'has_token' => !empty($tokenData['access_token']),
                'has_refresh_token' => !empty($googleUser->refreshToken),
            ]);

            return redirect()->route('user.show', $user)->with('success', 'Google Calendar connected successfully!');

        } catch (\Throwable $th) {
            \Log::error('Google OAuth callback error: ' . $th->getMessage(), [
                'exception' => $th,
                'trace' => $th->getTraceAsString(),
            ]);
            
            // Redirect to user profile if authenticated, otherwise to login
            return redirect()->route(Auth::check() ? 'user.show' : 'login', Auth::user())
                ->with('error', 'Failed to connect Google Calendar: ' . $th->getMessage());
        }
    }
}
