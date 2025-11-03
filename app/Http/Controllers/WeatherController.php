<?php

namespace App\Http\Controllers;

use App\Interfaces\WeatherProviderInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeatherController extends Controller
{
    public function __construct(
        protected WeatherProviderInterface $weatherService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Weather/Index',[
            'weather' => null,
            'city' => null,
        ]);
    }

    public function search(Request $request): Response
    {
        $request->validate(['city' => 'required|string|max:255']);

        $weather = $this->weatherService->getWeatherByCity($request->get('city'));

        return Inertia::render('Weather/Index', [
            'weather' => $weather,
            'city' => $request->get('city'),
        ]);
    }
}
