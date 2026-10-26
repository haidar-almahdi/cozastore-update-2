<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminRegisterController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class]);
    }

    /**
     * Show the admin registration form.
     */
    public function showRegistrationForm()
    {
        return view('admin.register');
    }

    /**
     * Handle admin registration request.
     */
    public function register(Request $request)
    {
        try {
            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = $this->create($request->all());

            return redirect()->route('admin.home')->with('success', 'User created successfully! Email: ' . $user->email . ', Role: ' . $user->role);
        } catch (\Exception $e) {
            Log::error('Admin registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Get a validator for an incoming admin registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'gender' => ['required', 'in:male,female'],
            'role' => ['required', 'in:admin,customer'],
        ]);
    }

    /**
     * Create a new admin user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'gender' => $data['gender'],
            'role' => $data['role'], // الأدمن يقدر يختار الـ role
        ]);
    }
}
