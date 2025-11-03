<?php

namespace Tests\Unit;

use App\DTO\WeatherData;
use App\Services\OpenWeatherService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenWeatherServiceTest extends TestCase
{
    public function test_get_weather_by_city_returns_successful_response()
    {
        Http::fake([
            'https://api.openweathermap.org/data/2.5/weather*' => Http::response([
                'cod' => 200,
                'name' => 'London',
                'sys' => ['country' => 'GB'],
                'main' => ['temp' => 15, 'humidity' => 70, 'feels_like' => 14, 'pressure' => 1012],
                'weather' => [['description' => 'clear sky', 'icon' => '01d']],
                'wind' => ['speed' => 5, 'deg' => 90],
                'clouds' => ['all' => 100],
            ], 200),
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $service = new OpenWeatherService(
            baseUrl: 'https://api.openweathermap.org/data/2.5',
            apiKey: 'fake-key',
            defaultCountry: 'GB',
        );
        $result = $service->getWeatherByCity('London');

        // Assert the DTO
        $this->assertInstanceOf(WeatherData::class, $result);
        $this->assertEquals('London', $result->city);
        $this->assertEquals('GB', $result->country);
        $this->assertEquals(15, $result->temperature);
        $this->assertEquals(14, $result->feelsLike);
        $this->assertEquals('clear sky', $result->description);
        $this->assertEquals(70, $result->humidity);
        $this->assertEquals(5, $result->windSpeed);
        $this->assertEquals(90, $result->windDeg);
        $this->assertEquals('01d', $result->icon);
    }

    public function test_get_weather_by_city_returns_error_response_on_failure()
    {
        $fakeErrorResponse = ['cod' => 404, 'message' => 'city not found'];

        Http::fake([
            'https://api.openweathermap.org/data/2.5/weather*' => Http::response($fakeErrorResponse, 404),
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $service = new OpenWeatherService(
            baseUrl: 'https://api.openweathermap.org/data/2.5',
            apiKey: 'fake-key',
            defaultCountry: 'GB',
        );

        $result = $service->getWeatherByCity('InvalidCity');

        $this->assertIsArray($result);
        $this->assertTrue($result['error']);
        $this->assertEquals('city not found', $result['message']);
    }
}
