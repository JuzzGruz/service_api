<?php

namespace App\Http\Controllers;

use App\Contracts\Weather\Factories\WeatherFactoryInterface;
use App\Contracts\Weather\Managers\WeatherManagerInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, WeatherManagerInterface $manager) : View {
        $validated = $request->validate([
            'coords' => 'nullable|string',
        ]);

        $city = $validated['coords'] ?? request()->cookie('city_coords');

        // Используем и сохраняем в cookie
        if ($city) {
            cookie()->queue(cookie('city_coords', $city, 60 * 24 * 30)); // 30 дней
            $city = json_decode($city, true); //Преобразуем в ассоциативный массив
        }

        $data = $manager->getWeatherAndCities($city);

        return view('dashboard', compact('data'));
    }
}
