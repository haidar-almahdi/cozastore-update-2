<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'recent_orders' => Order::with(['user', 'items.product'])
                ->latest()
                ->take(5)
                ->get(),
            'top_products' => DB::table('order_items')
                ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get(),
            'monthly_sales' => DB::table('orders')
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(total_amount) as total_sales'))
                ->where('status', 'completed')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(6)
                ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
