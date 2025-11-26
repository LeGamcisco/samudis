<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventClickjacking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Opsi 1: X-Frame-Options
        $response->headers->set('X-Frame-Options', 'DENY');

        // Opsi 2: Content-Security-Policy dengan frame-ancestors
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'none'");

        return $response;
    }
}
