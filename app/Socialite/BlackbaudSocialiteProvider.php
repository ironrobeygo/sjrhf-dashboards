<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;

class BlackbaudSocialiteProvider extends AbstractProvider
{
    protected $scopes = ['read_consitituents'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://oauth2.sky.blackbaud.com/authorization', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://oauth2.sky.blackbaud.com/token';
    }

    protected function getUserByToken($token)
    {
        return [
            'id' => $token, // or a hash of it, if needed
            'token' => $token,
            'email' => null,
            'name' => null,
        ];
    }    

    protected function mapUserToObject(array $user)
    {
        return (new \Laravel\Socialite\Two\User)->setRaw($user)->map([
            'id' => $user['id'],
            'token' => $user['token'],
            'name' => 'Blackbaud User',
            'email' => 'user_' . md5($user['token']) . '@blackbaud.local',
        ]);
    }    
}
