<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
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
        $favorites = auth()->user()->favorites()->with('product')->get();
        return view('shop.favorites.index', compact('favorites'));
    }

    /**
     * Toggle the favorite status of a product.
     */
    public function toggle($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Find the product by ID
            $product = Product::findOrFail($id);

            \Log::info('Favorite toggle request', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'user_email' => $user->email
            ]);

            // Check if already favorited
            $existingFavorite = $user->favorites()->where('product_id', $product->id)->first();

            if ($existingFavorite) {
                // Remove from favorites
                $existingFavorite->delete();
                $status = 'removed';
                \Log::info('Favorite removed', ['user_id' => $user->id, 'product_id' => $product->id]);
            } else {
                // Add to favorites
                $favorite = $user->favorites()->create(['product_id' => $product->id]);
                $status = 'added';
                \Log::info('Favorite added', ['user_id' => $user->id, 'product_id' => $product->id, 'favorite_id' => $favorite->id]);
            }

            $favoritesCount = $user->favorites()->count();

            // Return JSON response (for AJAX) or redirect back
            return response()->json([
                'status' => $status,
                'is_favorited' => $status === 'added',
                'favorites_count' => $favoritesCount,
                'message' => $status === 'added' ? 'Product added to favorites!' : 'Product removed from favorites!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Favorite toggle error', [
                'product_id' => $id ?? 'unknown',
                'user_id' => Auth::id() ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                'debug_info' => [
                    'product_id' => $id,
                    'user_authenticated' => Auth::check(),
                    'error_type' => get_class($e)
                ]
            ], 500);
        }
    }

    /**
     * Check if a product is favorited by the current user.
     */
    public function check(Product $product)
    {
        $isFavorited = auth()->user()->favorites()
            ->where('product_id', $product->id)
            ->exists();

        return response()->json(['is_favorited' => $isFavorited]);
    }

    /**
     * Get the count of user's favorites.
     */
    public function count()
    {
        $count = auth()->user()->favorites()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Remove a product from favorites.
     */
    public function remove($id)
    {
        $favorite = auth()->user()->favorites()->findOrFail($id);
        $favorite->delete();

        return redirect()->back()->with('success', 'Product removed from favorites.');
    }
}
