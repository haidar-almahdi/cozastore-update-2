<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from session, URL parameter, or default to 'en'
        $locale = $request->get('lang') ?? Session::get('locale') ?? 'en';

        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
