<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Number of registered users
        $userCount = User::count();

        // Most requested products (by order items count)
        $mostRequestedProducts = Product::withCount(['orderItems as orders_count' => function($q) {
            $q->select(DB::raw('sum(quantity)'));
        }])->orderByDesc('orders_count')->take(5)->get();

        // Most users who place orders (by order count)
        $topUsers = User::withCount('orders')->orderByDesc('orders_count')->take(5)->get();

        // Male/Female percentages
        $maleCount = User::where('gender', 'male')->count();
        $femaleCount = User::where('gender', 'female')->count();
        $totalGender = $maleCount + $femaleCount;
        $malePercent = $totalGender ? round(($maleCount / $totalGender) * 100, 1) : 0;
        $femalePercent = $totalGender ? round(($femaleCount / $totalGender) * 100, 1) : 0;

        // Profits (sum of all order totals with status completed or paid)
        $profits = Order::whereIn('status', ['completed', 'paid'])->sum('total_amount');

        return view('admin.home', compact(
            'userCount',
            'mostRequestedProducts',
            'topUsers',
            'malePercent',
            'femalePercent',
            'profits'
        ));
    }
}