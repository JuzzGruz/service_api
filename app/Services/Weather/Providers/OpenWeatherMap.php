<?php

namespace App\Services\Weather\Providers;

use App\Contracts\Weather\WeatherInterface;
use App\Services\DTOs\WeatherDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWeatherMap implements WeatherInterface
{
    private string $apiKey;
    private array $defaultCity = [
        "lat" => "55.755833333333",
        "lon" => "37.617777777778"
    ]; // Москва по умолчанию;

    public function __construct() {
        $this->apiKey = config('services.weather.providers.openweathermap.api_key');
    }

    public function getWeather(?array $city = null): ?WeatherDTO
    {
        $city = $city ?? $this->defaultCity;
        $lat = $city['lat'];
        $lon = $city['lon'];
        
        try {
            $response = Http::get(config('services.weather.providers.openweathermap.url'), [
                'lat' => $lat,
                'lon' => $lon,
                'APPID' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'ru',
            ])->throw(); // выбросит исключение, если статус >= 400
        }
        catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Weather API Ошибка соединения', [
                'error' => $e->getMessage(),
            ]);

            return new WeatherDTO(
                cod: 503,
                description: 'Ошибка соединения с Weather API'
            );
        } 
        catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Weather API Ошибка ответа', [
                'status' => $e->response->status(),
                'body' => $e->response->body(),
            ]);

            return new WeatherDTO(
                cod: $e->response->status(), // HTTP-код ошибки
                description: 'Ошибка ответа Weather API'
            );
        }
        catch (\Throwable $e) {
            Log::critical('Неожиданная ошибка при запросе погоды', [
                'error' => $e->getMessage()
            ]);

            return new WeatherDTO(
                cod: 500,
                description: 'Внутренняя ошибка при получении данных погоды'
            );
        }

        //Получаем декодированное тело ответа
        $data = $response->json();

        /**
         * апи возвращает код 200 даже при отсутсвии города
         * (код ошибки 404 прилетает в массиве с ключом cod)
         */
        if ((int)($data['cod'] ?? 0) !== 200) {
            Log::warning('Weather API Логическая ошибка', [
                'city' => $city,
                'cod' => $data['cod'] ?? null,
                'message' => $data['message'] ?? null,
            ]);
            
            return new WeatherDTO(
                cod: (int)($data['cod'] ?? 0),
            );
        }

        return new WeatherDTO(
            cod: $data['cod'],
            city: $data['name'] ?? null,
            temperature: $data['main']['temp'] ?? null,
            temp_min: $data['main']['temp_min'] ?? null,
            temp_max: $data['main']['temp_max'] ?? null,
            feels_like: $data['main']['feels_like'] ?? null,
            description: mb_ucfirst($data['weather'][0]['description'] ?? ''),
            humidity: $data['main']['humidity'] ?? null,
            wind_speed: $data['wind']['speed'] ?? null,
        );
    }
}
