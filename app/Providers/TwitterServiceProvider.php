<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public static function getFacadeAccessor(): string
    {
        return TwitterOAuth::class;
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('twitter', function () {

            return new TwitterOAuth(env('TWITTER_CLIENT_KEY'), env('TWITTER_CLIENT_SECRET'), env('TWITTER_CLIENT_ID_ACCESS_TOKEN'), env('TWITTER_CLIENT_ID_ACCESS_TOKEN_SECRET'));

        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['twitter'];
    }
}