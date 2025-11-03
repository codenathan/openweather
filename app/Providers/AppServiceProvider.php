<?php

namespace App\Providers;

use App\Interfaces\WeatherProviderInterface;
use App\Services\OpenWeatherService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WeatherProviderInterface::class, function ($app) {
           $config = config('services.openweather');
            return new OpenWeatherService(
                baseUrl: $config['base_url'],
                apiKey:  $config['api_key'],
                defaultCountry: $config['default_country']
            );

        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
