<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Если это JsonResponse
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true); // Берем только тело ответа
            return response()->json(
                $data,
                $response->getStatusCode(),
                [],
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT // Для читабельности
            );
        }

        // Если это обычный Response
        if ($response instanceof Response) {

            $content = $response->getContent();

            // Если это JSON
            $data = json_decode($content, true);
            if ($data === null) {
                // Если не JSON
                $data = $content;
            }

            return response()->json(
                $data,
                $response->getStatusCode(),
                [],
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );
        }

        // Всё остальное оставляем без изменений
        return $response;
    }
}
