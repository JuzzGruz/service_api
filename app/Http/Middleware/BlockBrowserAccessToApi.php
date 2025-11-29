<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Посредник блокирует доступ к апи из браузера
 */
class BlockBrowserAccessToApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        $isBrowser =
            str_contains($userAgent, 'mozilla') ||
            str_contains($userAgent, 'chrome') ||
            str_contains($userAgent, 'safari') ||
            str_contains($userAgent, 'firefox') ||
            $request->hasCookie('XSRF-TOKEN');

        if ($isBrowser) {
            return redirect('/');
        }

        return $response = $next($request);
    }
}
