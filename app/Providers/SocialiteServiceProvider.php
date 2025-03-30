<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use App\Socialite\BlackbaudSocialiteProvider;
use Illuminate\Http\Request;

class SocialiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving(SocialiteFactory::class, function ($socialite, $app) {
            $socialite->extend('blackbaud', function () use ($app) {
                $config = $app['config']['services.blackbaud'];

                return new BlackbaudSocialiteProvider(
                    $app->make(Request::class),
                    $config['client_id'],
                    $config['client_secret'],
                    $config['redirect']
                );
            });
        });
    }
}
