<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (Auth::check()) {
            // Set bahasa aplikasi sesuai kolom 'locale' di database user
            App::setLocale(Auth::user()->locale);
        } elseif (session()->has('locale')) {
            // Jika belum login, gunakan bahasa dari session (misal di halaman login)
            App::setLocale(session()->get('locale'));
        }

        return $next($request);
    }
}