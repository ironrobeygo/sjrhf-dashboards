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
        $response = $this->getHttpClient()->get(
            'https://api.sky.blackbaud.com/constituent/v1/constituents/me',
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Bb-Api-Subscription-Key' => config('services.blackbaud.subscription_key'),
                ],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['first']['value'] . ' ' . $user['last']['value'],
            'email' => $user['email']['address'] ?? null,
        ]);
    }
}
