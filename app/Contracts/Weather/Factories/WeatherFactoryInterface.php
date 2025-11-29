<?php

namespace App\Contracts\Weather\Factories;

use App\Contracts\Files\CitiesInterface;
use App\Contracts\Weather\WeatherInterface;

interface WeatherFactoryInterface
{
    public function makeWeatherService() : WeatherInterface;
    public function makeCitiesService() : CitiesInterface;
}
