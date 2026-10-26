<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's favorite products.
     */
    public function index()
    {
        $favorites = Auth::user()->favoritedProducts()->paginate(12);
        return view('shop.favorites.index', compact('favorites'));
    }

    /**
     * Toggle the favorite status of a product.
     */
    public function toggle(Product $product)
    {
        $user = Auth::user();

        // Toggle favorite status
        if ($user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            $user->favoriteProducts()->detach($product->id);
            $status = 'removed';
        } else {
            $user->favoriteProducts()->attach($product->id);
            $status = 'added';
        }

        // Return JSON response (for AJAX) or redirect back
        if (request()->expectsJson()) {
            return response()->json([
                'status' => $status,
                'is_favorited' => $status === 'added',
                'favorites_count' => $user->favoriteProducts()->count(),
            ]);
        }

        return back()->with('status', "Product {$status} from favorites!");
    }

    /**
     * Check if a product is favorited by the current user.
     */
    public function check(Product $product)
    {
        $isFavorited = Auth::user()->favoritedProducts()
            ->where('product_id', $product->id)
            ->exists();

        return response()->json(['is_favorited' => $isFavorited]);
    }
}