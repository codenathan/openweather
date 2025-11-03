<?php

namespace App\DTO;

readonly class WeatherData
{
    public function __construct(
        public string  $city,
        public string  $country,
        public float   $temperature,
        public float   $feelsLike,
        public string  $description,
        public int     $humidity,
        public float   $windSpeed,
        public int     $windDeg,
        public int     $pressure,
        public int     $clouds,
        public ?string $icon = null,
    ) {}

    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'country' => $this->country,
            'temperature' => $this->temperature,
            'feels_like' => $this->feelsLike,
            'description' => $this->description,
            'humidity' => $this->humidity,
            'wind_speed' => $this->windSpeed,
            'wind_deg' => $this->windDeg,
            'pressure' => $this->pressure,
            'clouds' => $this->clouds,
            'icon' => $this->icon,
        ];
    }
}
