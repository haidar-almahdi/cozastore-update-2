<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventRoleManipulation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // إذا كان الـ request فيه role parameter
        if ($request->has('role')) {
            // تحقق من إن المستخدم أدمن
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                // إذا مش أدمن، شيل الـ role من الـ request
                $request->request->remove('role');

                // أو ارجع error
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Unauthorized to set user role'
                    ], 403);
                }

                return redirect()->back()->withErrors([
                    'role' => 'You are not authorized to set user roles.'
                ]);
            }
        }

        return $next($request);
    }
}
