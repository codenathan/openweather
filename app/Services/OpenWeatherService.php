<?php

namespace App\Services;

use App\Interfaces\WeatherProviderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenWeatherService implements WeatherProviderInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $defaultCountry;

    public function __construct()
    {
        $this->baseUrl = "https://api.openweathermap.org/data/2.5";
        $this->apiKey = config('services.openweather.api_key');
        $this->defaultCountry = "GB";
    }

    public function getWeatherByCity(string $city): array
    {
        $query = "{$city},{$this->defaultCountry}";

        return Cache::remember("weather_{$query}", now()->addMinutes(10), function () use ($query) {
            $response = Http::get("{$this->baseUrl}/weather", [
                'q' => $query,
                'appid' => $this->apiKey,
                'units' => 'metric',
            ]);

            return $response->successful()
                ? $response->json()
                : ['error' => true, 'message' => $response->json('message')];
        });
    }
}
