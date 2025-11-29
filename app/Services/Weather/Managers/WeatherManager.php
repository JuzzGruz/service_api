<?php

namespace App\Services\Weather\Managers;

use RuntimeException;
use App\Services\DTOs\WeatherDTO;
use Illuminate\Support\Facades\Log;
use App\Contracts\Files\CitiesInterface;
use App\Contracts\Weather\WeatherInterface;
use App\Contracts\Weather\Managers\WeatherManagerInterface;

class WeatherManager implements WeatherManagerInterface
{
    private CitiesInterface $citiesService;

    /** @var WeatherInterface[] */
    private array $weatherServices;

    public function __construct(
        CitiesInterface $citiesService,
        WeatherInterface ...$weatherServices
    ) {
        $this->citiesService = $citiesService;
        $this->weatherServices = $weatherServices;
    }

    public function getWeatherAndCities(?array $city = null): array
    {
        $manager['cities'] = $this->citiesService->getAll();
        $manager['weather'] = $this->getWeatherFallback($city);

        return $manager;
    }

    private function getWeatherFallback(?array $city): WeatherDTO
    {
        foreach ($this->weatherServices as $service) {

            try {
                $result = $service->getWeather($city);

                if (!empty($result)) {
                    return $result;
                }

                // Логируем случай, когда сервис вернул пустой ответ
                Log::warning("Сервис погоды отработал, но вернул пустой результат.", [
                    'service' => get_class($service),
                    'city'    => $city,
                ]);

            } catch (\Throwable $e) {

                // Логируем ошибку сервиса с текстом исключения
                Log::error("Сервис погоды не отработал.", [
                    'service' => get_class($service),
                    'city'    => $city,
                    'error'   => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);
            }
        }

        // Если ни один сервис не сработал
        Log::critical("Все сервисы погоды не отработали.", [
            'city' => $city,
        ]);

        throw new RuntimeException("Все погодные сервисы недоступны");
    }
}
