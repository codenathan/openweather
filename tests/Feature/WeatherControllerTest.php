<?php

namespace Tests\Feature;

use App\Interfaces\WeatherProviderInterface;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;

class WeatherControllerTest extends TestCase
{
    public function test_weather_index_page_renders_correctly()
    {
        $this->get(route('weather.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) =>
            $page->component('Weather/Index')
                ->where('weather', null)
                ->where('city', null)
            );
    }

    public function test_weather_search_returns_weather_data()
    {
        $fakeWeather = [
            'cod' => 200,
            'name' => 'London',
            'sys' => ['country' => 'GB'],
            'main' => ['temp' => 15, 'humidity' => 70, 'feels_like' => 14],
            'weather' => [['description' => 'clear sky', 'icon' => '01d']],
            'wind' => ['speed' => 5, 'deg' => 90],
        ];

        // Mock the WeatherProviderInterface
        $this->instance(
            WeatherProviderInterface::class,
            Mockery::mock(WeatherProviderInterface::class, function ($mock) use ($fakeWeather) {
                $mock->shouldReceive('getWeatherByCity')
                    ->once()
                    ->with('London')
                    ->andReturn($fakeWeather);
            })
        );

        $response = $this->post(route('weather.search'), ['city' => 'London']);

        $response->assertStatus(200)
            ->assertInertia(fn (Assert $page) =>
            $page->component('Weather/Index')
                ->has('weather')
                ->where('weather.name', 'London')
                ->where('weather.sys.country', 'GB')
                ->where('weather.main.temp', 15)
                ->where('weather.weather.0.description', 'clear sky')
                ->where('city', 'London')
            );
    }

    public function test_weather_search_returns_error_for_invalid_city()
    {
        $fakeError = [
            'error' => true,
            'message' => 'city not found'
        ];

        // Mock the WeatherProviderInterface
        $this->instance(
            WeatherProviderInterface::class,
            Mockery::mock(WeatherProviderInterface::class, function ($mock) use ($fakeError) {
                $mock->shouldReceive('getWeatherByCity')
                    ->once()
                    ->with('InvalidCity')
                    ->andReturn($fakeError);
            })
        );

        $response = $this->post(route('weather.search'), ['city' => 'InvalidCity']);

        $response->assertStatus(200)
            ->assertInertia(fn ($page) =>
            $page->component('Weather/Index')
                ->has('weather')
                ->where('weather.error', true)
                ->where('weather.message', 'city not found')
                ->where('city', 'InvalidCity')
            );
    }

}
