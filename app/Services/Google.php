<?php

namespace App\Services;

use App\Models\User;

class Google
{
    protected $client;

    function __construct()
    {
        $client = new \Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setScopes(config('services.google.scopes'));
        $client->setApprovalPrompt(config('services.google.approval_prompt'));
        $client->setAccessType(config('services.google.access_type'));
        $client->setIncludeGrantedScopes(config('services.google.include_granted_scopes'));
        $this->client = $client;
    }

    public function connectUsing($token, $refresh_token)
    {
        $this->client->setAccessToken($token);
        if ($this->client->isAccessTokenExpired())
            if($refresh_token) {
                $token = $this->client->refreshToken($refresh_token);
                $this->client->setAccessToken($token);
            }

        return $this;
    }

    public function connectUser(User $user){
        try {
            $token = $user->google_token;
            $this->client->setAccessToken($token);
            if ( $this->client->isAccessTokenExpired() && ($user->google_refresh_token) ) {
                $refresh_token = $user->google_refresh_token;
                $token = $this->client->refreshToken($refresh_token);
                $this->client->setAccessToken($token);
                $user->update([
                    'google_token' => $token
                ]);
            }
        }catch (\Exception $e){ }
        return $this;
    }

    /*
    public function connectWithSynchronizable($synchronizable)
    {
        $token = $this->getTokenFromSynchronizable($synchronizable);

        return $this->connectUsing($token);
    }

    protected function getTokenFromSynchronizable($synchronizable)
    {
        switch (true) {
            case $synchronizable instanceof GoogleAccount:
                return $synchronizable->token;

            case $synchronizable instanceof Calendar:
                return $synchronizable->googleAccount->token;

            default:
                throw new \Exception("Invalid Synchronizable");
        }
    }
    */

    public function revokeToken($token = null)
    {
        $token = $token ?? $this->client->getAccessToken();

        return $this->client->revokeToken($token);
    }

    public function service($service)
    {
        $classname = "Google_Service_$service";

        return new $classname($this->client);
    }

    public function __call($method, $args)
    {
        if (! method_exists($this->client, $method)) {
            throw new \Exception("Call to undefined method '{$method}'");
        }

        return call_user_func_array([$this->client, $method], $args);
    }
}
