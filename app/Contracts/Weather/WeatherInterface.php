<?php

namespace App\Contracts\Weather;

use App\Services\DTOs\WeatherDTO;

interface WeatherInterface
{
    public function getWeather(?array $city = null): ?WeatherDTO;
}
