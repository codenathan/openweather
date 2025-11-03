<?php

namespace App\Services;

use App\Interfaces\WeatherProviderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenWeatherService implements WeatherProviderInterface
{

    public function __construct(public string $baseUrl, public string $apiKey, public string $defaultCountry)
    {
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
