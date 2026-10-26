<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    protected function blackFridayWindow(?Carbon $baseDate = null): array
    {
        $baseDate = $baseDate ?: now();
        $settings = Product::getBlackFridaySettings();
        $month = (int) $settings['month'];
        $startDay = (int) $settings['start_day'];
        $endDay = (int) $settings['end_day'];

        $year = (int) $baseDate->year;
        $start = Carbon::create($year, $month, $startDay, 0, 0, 0);
        $end = Carbon::create($year, $month, $endDay, 23, 59, 59);

        if ($baseDate->greaterThan($end)) {
            $start = $start->copy()->addYear();
            $end = $end->copy()->addYear();
        }

        return [$start, $end];
    }

    public function blackFriday(Request $request)
    {
        [$startAt, $endAt] = $this->blackFridayWindow();
        $isBlackFridayActive = now()->between($startAt, $endAt);
        $bfSettings = Product::getBlackFridaySettings();

        $products = Product::with('category')
            ->where('is_active', true);

        // Safety fallback: if migration was not applied on this DB yet, avoid crashing page.
        if (Schema::hasColumn('products', 'black_friday_enabled')) {
            $products->where('black_friday_enabled', true);
        }

        $products = $products->latest()->paginate(24);

        return view('shop.products.black-friday', compact('products', 'isBlackFridayActive', 'startAt', 'endAt', 'bfSettings'));
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Filter by category if provided
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        // if ($request->has('min_price')) {
        //     $query->where('price', '>=', $request->min_price);
        // }
        // if ($request->has('max_price')) {
        //     $query->where('price', '<=', $request->max_price);
        // }

        // Sort products
        // $sort = $request->get('sort', 'newest');
        // switch ($sort) {
        //     case 'price_low':
        //         $query->orderBy('price', 'asc');
        //         break;
        //     case 'price_high':
        //         $query->orderBy('price', 'desc');
        //         break;
        //     case 'name':
        //         $query->orderBy('name', 'asc');
        //         break;
        //     default:
        //         $query->orderBy('created_at', 'desc');
        // }

        $products = $query->paginate(25);
        $categories = Category::withCount(['products' => function($query) {
            $query->where('is_active', true);
        }])->get();

        return view('shop.products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        if (request()->ajax()) {
            return response()->json([
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image_url' => $product->image_url,
                'sizes' => $product->sizes,
                'colors' => $product->colors,
                'stock' => $product->stock
            ]);
        }

        // Eager-load comments with users
        $product->load(['comments.user']);

        // Ratings aggregates
        $averageRating = round((float) $product->ratings()->avg('stars'), 1);
        $ratingsCount = (int) $product->ratings()->count();
        $userRating = null;
        if (auth()->check()) {
            $userRating = optional($product->ratings()->where('user_id', auth()->id())->first())->stars;
        }

        return view('shop.products.show', [
            'product' => $product,
            'averageRating' => $averageRating,
            'ratingsCount' => $ratingsCount,
            'userRating' => $userRating,
        ]);
    }
}
