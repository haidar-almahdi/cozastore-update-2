<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'driver') {
            return redirect()->route('shop.home')->with('error', 'Unauthorized access. Driver account required.');
        }

        return $next($request);
    }
}
