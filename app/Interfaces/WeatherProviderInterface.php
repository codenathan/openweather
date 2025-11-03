<?php


namespace App\Interfaces;

use App\DTO\WeatherData;

interface WeatherProviderInterface
{
    public function getWeatherByCity(string $city): WeatherData|array;
}
