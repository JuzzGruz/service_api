<?php

namespace App\Contracts\Weather\Managers;

interface WeatherManagerInterface
{
    public function getWeatherAndCities(?array $city = null): array;
}
