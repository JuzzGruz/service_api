<?php

namespace App\Services\DTOs;

class WeatherDTO
{
    public function __construct(
        public $cod,
        public ?string $city = null,
        public ?float $temperature = null,
        public ?float $temp_min = null,
        public ?float $temp_max = null,
        public ?float $feels_like = null,
        public ?string $description = null,
        public ?float $humidity = null,
        public ?float $wind_speed = null,
    ) {}
}
