<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika APP_MAINTENANCE bernilai 'on' dan rute saat ini bukan halaman maintenance itu sendiri
        if (env('APP_MAINTENANCE') === 'on' && !$request->is('maintenance')) {
            return redirect()->route('maintenance');
        }

        return $next($request);
    }
}