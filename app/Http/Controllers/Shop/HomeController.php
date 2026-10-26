<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected function getCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id(), 'status' => 'active'],
                ['session_id' => session()->getId()]
            );
        } else {
            $cart = Cart::firstOrCreate(
                ['session_id' => session()->getId(), 'status' => 'active']
            );
        }

        return $cart;
    }

    /**
     * Display the homepage.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('category')->latest()->paginate(12);
        return view('shop.home', compact('categories', 'products'));
    }

    public function products(Request $request)
    {
        $query = Product::where('is_active', true)->with('category');

        // Apply filters
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->latest();
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('home', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('shop.product-details', compact('product', 'relatedProducts'));
    }

    public function category(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $products = $category->products()
            ->where('is_active', true)
            ->paginate(12);

        return view('shop.category', compact('category', 'products'));
    }
}
