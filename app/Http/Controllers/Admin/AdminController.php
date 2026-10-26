<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        try {
            // Get basic counts
            $userCount = User::where('role', '!=', 'admin')->count();
            $totalUsers = User::count();
            $adminCount = User::where('role', 'admin')->count();
            $productCount = Product::count();
            $categoryCount = Category::count();
            $orderCount = Order::count();
            $messageCount = ContactMessage::count();
            $unreadMessageCount = ContactMessage::where('is_read', false)->count();

            // Get profits from orders
            $totalProfits = Order::sum('total_amount') ?? 0;
            $monthlyProfits = Order::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->sum('total_amount') ?? 0;

            // Get gender statistics
            $maleCount = User::where('gender', 'male')->count();
            $femaleCount = User::where('gender', 'female')->count();
            $totalGenderUsers = $maleCount + $femaleCount;

            $malePercent = $totalGenderUsers > 0 ? round(($maleCount / $totalGenderUsers) * 100, 1) : 0;
            $femalePercent = $totalGenderUsers > 0 ? round(($femaleCount / $totalGenderUsers) * 100, 1) : 0;

            // Get top products by order count
            $topProducts = Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')
                ->take(5)
                ->get();

            // Get top users by order count (excluding admins)
            $topUsers = User::withCount('orders')
                ->where('role', '!=', 'admin')
                ->orderBy('orders_count', 'desc')
                ->take(5)
                ->get();

            // Get recent orders
            $recentOrders = Order::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Get recent messages
            $recentMessages = ContactMessage::orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $loyaltyNotifications = Auth::user()
                ? Auth::user()->unreadNotifications()
                    ->where('type', \App\Notifications\AdminWalletConversionNotification::class)
                    ->latest()
                    ->take(5)
                    ->get()
                : collect();

            return view('admin.home', compact(
                'userCount',
                'totalUsers',
                'adminCount',
                'productCount',
                'categoryCount',
                'orderCount',
                'messageCount',
                'unreadMessageCount',
                'totalProfits',
                'monthlyProfits',
                'malePercent',
                'femalePercent',
                'topProducts',
                'topUsers',
                'recentOrders',
                'recentMessages',
                'loyaltyNotifications'
            ));
        } catch (\Exception $e) {
            // In case of any error, return view with default values
            return view('admin.home', [
                'userCount' => 0,
                'totalUsers' => 0,
                'adminCount' => 0,
                'productCount' => 0,
                'categoryCount' => 0,
                'orderCount' => 0,
                'messageCount' => 0,
                'unreadMessageCount' => 0,
                'totalProfits' => 0,
                'monthlyProfits' => 0,
                'malePercent' => 0,
                'femalePercent' => 0,
                'topProducts' => collect(),
                'topUsers' => collect(),
                'recentOrders' => collect(),
                'recentMessages' => collect(),
                'loyaltyNotifications' => collect()
            ]);
        }
    }
}
