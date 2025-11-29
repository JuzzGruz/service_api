<?php

namespace App\Providers;

use App\Contracts\Files\CitiesInterface;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\Weather\Factories\OWMFactory;
use App\Services\Weather\Managers\WeatherManager;
use App\Contracts\Weather\Managers\WeatherManagerInterface;
use App\Contracts\Weather\Factories\WeatherFactoryInterface;
use App\Contracts\Weather\WeatherInterface;
use App\Services\CitiesService;
use App\Services\Weather\Providers\OpenWeatherMap;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WeatherFactoryInterface::class, function($app) {
            return new OWMFactory();
        });

        /**
         * Сервис городов
         */
        $this->app->singleton(CitiesInterface::class, function($app) {
            return new CitiesService(config('services.weather.cities'));
        });

        /**
         * Основный погодный сервис
         */
        $this->app->singleton(WeatherInterface::class, function($app) {
            return new OpenWeatherMap();
        });

        /**
         * Менеджер погоды
         */
        $this->app->singleton(WeatherManagerInterface::class, function($app) {
            return new WeatherManager(
                $app->make(CitiesInterface::class), // 1 аргумент сервис городов
                $app->make(WeatherInterface::class), // 2 аргумент основной сервис погоды
                //след. аргументами добавляем резервные сервисы реализующие WeatherInterface
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return [
                Limit::perSecond(2)->by($request->user()?->id ?: $request->ip())->response(
                    function (Request $request, array $headers) {
                        return response()->json([
                            'message' => 'Слишком много запросов'
                        ], 429);
                    }),
                Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())->response(
                function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Слишком много запросов'
                    ], 429);
                })];
        });
    }
}
