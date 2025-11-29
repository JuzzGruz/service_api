<?php

namespace App\Services\Weather\Factories;

use App\Contracts\Files\CitiesInterface;
use App\Services\CitiesService;
use App\Contracts\Weather\Factories\WeatherFactoryInterface;
use App\Contracts\Weather\WeatherInterface;
use App\Services\Weather\Providers\OpenWeatherMap;

class OWMFactory implements WeatherFactoryInterface
{
    private ?WeatherInterface $weatherService = null;
    private ?CitiesInterface $citiesService = null;

    public function makeCitiesService(): CitiesInterface
    {
        if ($this->citiesService === null) {
            $this->citiesService = new CitiesService(
                config('services.weather.cities')
            );
        }

        return $this->citiesService;
    }

    public function makeWeatherService(): WeatherInterface
    {
        if ($this->weatherService === null) {
            $this->weatherService = new OpenWeatherMap();
        }

        return $this->weatherService;
    }
}
