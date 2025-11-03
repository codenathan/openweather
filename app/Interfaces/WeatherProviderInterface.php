<?php


namespace App\Interfaces;

interface WeatherProviderInterface
{
    public function getWeatherByCity(string $city): array;
}
