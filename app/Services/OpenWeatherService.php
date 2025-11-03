<?php

namespace App\Services;

use App\DTO\WeatherData;
use App\Interfaces\WeatherProviderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OpenWeatherService implements WeatherProviderInterface
{

    public function __construct(public string $baseUrl, public string $apiKey, public string $defaultCountry)
    {
    }

    public function getWeatherByCity(string $city): WeatherData|array
    {
        $query = "{$city},{$this->defaultCountry}";

        return Cache::remember("weather_{$query}", now()->addMinutes(10), function () use ($query) {
            $response = Http::get("{$this->baseUrl}/weather", [
                'q' => $query,
                'appid' => $this->apiKey,
                'units' => 'metric',
            ]);

            if (!$response->successful()) {
                return [
                    'error' => true,
                    'message' => $response->json('message', 'Unable to fetch weather data'),
                ];
            }

            $data = $response->json();

            return new WeatherData(
                city: $data['name'],
                country: $data['sys']['country'],
                temperature: $data['main']['temp'],
                feelsLike: $data['main']['feels_like'],
                description: $data['weather'][0]['description'],
                humidity: $data['main']['humidity'],
                windSpeed: $data['wind']['speed'],
                windDeg: $data['wind']['deg'],
                pressure: $data['main']['pressure'],
                clouds: $data['clouds']['all'],
                icon: $data['weather'][0]['icon'] ?? null,
            );
        });
    }
}
