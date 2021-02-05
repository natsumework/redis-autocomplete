<?php


namespace Natsumework\RedisAutocomplete;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AutoCompleteServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/redis-autocomplete.php', 'redis-autocomplete'
        );

        $this->app->singleton(Autocomplete::class, function ($app) {
            return new Autocomplete(config('redis-autocomplete'));
        });
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/redis-autocomplete.php' => config_path('redis-autocomplete.php'),
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Autocomplete::class];
    }
}
