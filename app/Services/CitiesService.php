<?php

namespace App\Services;

use App\Contracts\Files\CitiesInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CitiesService implements CitiesInterface
{

    public function __construct(
        private string $path,
        private int $cacheTtl = 3600, // TTL кеша в секундах
    ) {}

    /**
     * Проверка и парсинг файла
     */
    private function loadCitiesFile(): array
    {
        if (!Storage::disk('public')->exists($this->path)) {
            $message = "Файл с городами не найден: {$this->path}";
            Log::error($message);
            return ['message' => $message];
        }

        $json = Storage::disk('public')->get($this->path);
        $cities = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = 'Ошибка парсинга JSON в файле ' . $this->path . ': ' . json_last_error_msg();
            Log::error($message);
            return ['message' => $message];
        }

        return $cities;
    }
    
    /**
     * Получить массив всех городов
     */
    public function getAll(): array
    {
        try {
            $lastModified = Storage::disk('public')->lastModified($this->path);
            $cacheKey = "cities_indexed_{$lastModified}";
            $cities = $this->loadCitiesFile();

            if (isset($cities['message'])) {
                return $cities;
            }

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($cities) {
                return $cities;
            });

        } catch (\Throwable $e) {
            $message = "Ошибка при обработке файла городов: " . $e->getMessage();
            Log::error($message);
            return ['message' => $message];
        }
    }

}
