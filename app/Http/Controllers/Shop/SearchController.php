<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Search for products and categories
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // all, products, categories

        if (empty($query)) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'total' => 0
            ]);
        }

        $results = [
            'products' => [],
            'categories' => [],
            'total' => 0
        ];

        // Search in products
        if ($type === 'all' || $type === 'products') {
            $products = Product::where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->with('category')
                ->limit(10)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => Str::limit($product->description, 60),
                        'price' => $product->price,
                        'image' => $product->image_url,
                        'category' => $product->category->name ?? '',
                        'url' => route('shop.products.show', $product),
                        'type' => 'product'
                    ];
                });

            $results['products'] = $products;
        }

        // Search in categories
        if ($type === 'all' || $type === 'categories') {
            $categories = Category::where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->withCount('products')
                ->limit(5)
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'description' => Str::limit($category->description, 60),
                        'products_count' => $category->products_count,
                        'image' => $category->image ? asset('storage/' . $category->image) : null,
                        'url' => route('shop.products.index', ['category' => $category->slug]),
                        'type' => 'category'
                    ];
                });

            $results['categories'] = $categories;
        }

        $results['total'] = count($results['products']) + count($results['categories']);

        return response()->json($results);
    }

    /**
     * Get search suggestions (autocomplete)
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // Product suggestions
        $productSuggestions = Product::where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->select('name')
            ->distinct()
            ->limit(5)
            ->pluck('name')
            ->map(function($name) {
                return [
                    'text' => $name,
                    'type' => 'product'
                ];
            });

        // Category suggestions
        $categorySuggestions = Category::where('name', 'LIKE', "%{$query}%")
            ->select('name')
            ->distinct()
            ->limit(3)
            ->pluck('name')
            ->map(function($name) {
                return [
                    'text' => $name,
                    'type' => 'category'
                ];
            });

        $suggestions = $productSuggestions->concat($categorySuggestions)->take(8);

        return response()->json($suggestions);
    }

    /**
     * Display search results page
     */
    public function results(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        if (empty($query)) {
            return redirect()->route('shop.products.index');
        }

        // Get search results
        $products = collect();
        $categories = collect();

        if ($type === 'all' || $type === 'products') {
            $products = Product::where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->with('category')
                ->paginate(12);
        }

        if ($type === 'all' || $type === 'categories') {
            $categories = Category::where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->withCount('products')
                ->get();
        }

        return view('shop.search.results', compact('query', 'products', 'categories', 'type'));
    }
}
