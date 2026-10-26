<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'showLogoutForm']);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show the logout confirmation form.
     */
    public function showLogoutForm()
    {
        return view('auth.logout');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if user is admin and redirect accordingly
            if (Auth::user()->isAdmin()) {
                $redirectTo = route('admin.home');
            } elseif (Auth::user()->isDriver()) {
                $redirectTo = route('driver.home');
            } else {
                $redirectTo = $this->redirectTo;
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => $redirectTo
                ]);
            }

            return redirect()->intended($redirectTo);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.'
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('shop.home');
    }
}
