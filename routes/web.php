<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\FavoriteController;
use App\Http\Controllers\Shop\ContactController;
use App\Http\Controllers\Shop\SearchController;
use App\Http\Controllers\Shop\AiAssistantController;
use App\Http\Controllers\Shop\ProfileController;
use App\Http\Controllers\Shop\MyMessagesController;
use App\Http\Controllers\Shop\AboutController;
use App\Http\Controllers\Shop\ProductCommentController;
use App\Http\Controllers\Shop\ProductRatingController;
use App\Http\Controllers\Shop\SizeGuideController as ShopSizeGuideController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LoyaltyController as AdminLoyaltyController;
use App\Http\Controllers\Admin\SizeGuideController as AdminSizeGuideController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\DiscountSettingsController;
use App\Http\Controllers\Admin\QuickOfferController;
use App\Http\Controllers\Driver\OrderController as DriverOrderController;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

// Quick setup route to create categories
Route::get('/quick-setup', function() {
    try {
        $count = Category::count();

        if ($count == 0) {
            $categories = [
                ['name' => 'Women\'s Fashion', 'slug' => 'womens-fashion', 'description' => 'Latest fashion trends for women', 'is_active' => true],
                ['name' => 'Men\'s Fashion', 'slug' => 'mens-fashion', 'description' => 'Stylish clothing for men', 'is_active' => true],
                ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Fashion accessories and jewelry', 'is_active' => true],
                ['name' => 'Shoes', 'slug' => 'shoes', 'description' => 'Comfortable and stylish footwear', 'is_active' => true],
                ['name' => 'Bags', 'slug' => 'bags', 'description' => 'Handbags, backpacks and more', 'is_active' => true]
            ];

            foreach ($categories as $category) {
                Category::create($category);
            }

            return '<h1>✅ Categories created successfully!</h1><p><a href="/admin">Go to Admin Panel</a></p>';
        } else {
            return '<h1>❌ Categories already exist!</h1><p><a href="/admin">Go to Admin Panel</a></p>';
        }
    } catch (Exception $e) {
        return '<h1>❌ Error: ' . $e->getMessage() . '</h1>';
    }
});

Route::get('/', [HomeController::class, 'index'])->name('shop.home');



// Authentication Routes
Auth::routes();

// Additional logout routes
Route::post('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::post('/logout-form', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.form');

// Shop Routes
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/deals/black-friday', [ProductController::class, 'blackFriday'])->name('deals.black-friday');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/size-guide', [ShopSizeGuideController::class, 'show'])->name('products.size-guide');
    Route::get('/products/{product}/size-info', [ShopSizeGuideController::class, 'sizeInfo'])->name('products.size-info');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    // Search
    Route::get('/search', [SearchController::class, 'results'])->name('search');
    Route::get('/search/results', [SearchController::class, 'results'])->name('search.results');
    Route::get('/api/search', [SearchController::class, 'search'])->name('search.api');
    Route::get('/api/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
    Route::post('/api/assistant/chat', [AiAssistantController::class, 'chat'])->name('assistant.chat');

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        // Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::get('/cart/index', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
        Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

        // Checkout & Orders
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
        Route::get('/favorites/index', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
        Route::post('/favorites/{id}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

        // Product Comments & Ratings
        Route::post('/products/{product}/comments', [ProductCommentController::class, 'store'])->name('products.comments.store');
        Route::post('/products/{product}/ratings', [ProductRatingController::class, 'store'])->name('products.ratings.store');

        // Loyalty - My Points (user facing)
        Route::get('/my-points', [\App\Http\Controllers\Shop\LoyaltyController::class, 'index'])->name('my-points.index');

        // Profile
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Messages
        Route::get('/my-messages', [MyMessagesController::class, 'index'])->name('my-messages.index');
        Route::get('/my-messages/{message}', [MyMessagesController::class, 'show'])->name('my-messages.show');
    });
});

// Admin register route (outside middleware)
Route::get('/admin/register', function () {
    return view('auth.register');
})->name('admin.register.form');

Route::post('/admin/register', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'admin',
    ]);

    Auth::login($user);

    return redirect()->route('admin.home');
})->name('admin.register');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');

    // Messages
    Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [ContactMessageController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{message}/read', [ContactMessageController::class, 'markAsRead'])->name('messages.read');
    Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

    // Products
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Quick Offers
    Route::get('/offers/quick', [QuickOfferController::class, 'index'])->name('offers.quick.index');
    Route::patch('/offers/quick/{product}', [QuickOfferController::class, 'update'])->name('offers.quick.update');

    // Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/drivers/create', [AdminDriverController::class, 'create'])->name('drivers.create');
    Route::post('/drivers', [AdminDriverController::class, 'store'])->name('drivers.store');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Loyalty
    Route::get('/loyalty', [AdminLoyaltyController::class, 'index'])->name('loyalty.index');
    Route::get('/loyalty/users/{user}', [AdminLoyaltyController::class, 'show'])->name('loyalty.show');

    // Size Guides
    Route::get('/size-guides', [AdminSizeGuideController::class, 'index'])->name('size-guides.index');
    Route::get('/size-guides/create', [AdminSizeGuideController::class, 'create'])->name('size-guides.create');
    Route::post('/size-guides', [AdminSizeGuideController::class, 'store'])->name('size-guides.store');
    Route::get('/size-guides/{sizeGuide}/edit', [AdminSizeGuideController::class, 'edit'])->name('size-guides.edit');
    Route::put('/size-guides/{sizeGuide}', [AdminSizeGuideController::class, 'update'])->name('size-guides.update');
    Route::delete('/size-guides/{sizeGuide}', [AdminSizeGuideController::class, 'destroy'])->name('size-guides.destroy');
    Route::post('/size-guides/{sizeGuide}/duplicate', [AdminSizeGuideController::class, 'duplicate'])->name('size-guides.duplicate');
    Route::post('/size-guides/generate-defaults', [AdminSizeGuideController::class, 'generateDefaults'])->name('size-guides.generate-defaults');

    // Discount Settings
    Route::get('/settings/discounts', [DiscountSettingsController::class, 'edit'])->name('settings.discounts.edit');
    Route::put('/settings/discounts', [DiscountSettingsController::class, 'update'])->name('settings.discounts.update');
});

Route::prefix('driver')->middleware(['auth', 'driver'])->name('driver.')->group(function () {
    Route::get('/', [DriverOrderController::class, 'index'])->name('home');
    Route::get('/orders', [DriverOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [DriverOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/accept', [DriverOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/delivered', [DriverOrderController::class, 'markDelivered'])->name('orders.delivered');
});
