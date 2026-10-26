<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('products')->latest()->get();

        // Calculate statistics
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $activeCategories = Category::where('is_active', true)->count();
        $inactiveCategories = Category::where('is_active', false)->count();

        // Find category with most products
        $mostProductsCount = 0;
        if ($totalCategories > 0) {
            $categoryWithMostProducts = Category::withCount('products')
                ->orderBy('products_count', 'desc')
                ->first();
            $mostProductsCount = $categoryWithMostProducts ? $categoryWithMostProducts->products_count : 0;
        }

        return view('admin.categories.index', compact(
            'categories',
            'totalCategories',
            'totalProducts',
            'activeCategories',
            'inactiveCategories',
            'mostProductsCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|file|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();

            // Check if it's a valid image extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return back()->withErrors(['image' => 'Please upload a valid image file (jpg, jpeg, png, gif, webp)']);
            }

            // Create filename manually
            $filename = 'category_' . time() . '_' . uniqid() . '.' . $extension;

            // Move file to public/storage/categories (where storage link points)
            $destinationPath = public_path('storage/categories');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['image'] = 'categories/' . $filename;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    // public function show(Category $category)
    // {
    //     return view('admin.categories.show', compact('category'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|file|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();

            // Check if it's a valid image extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return back()->withErrors(['image' => 'Please upload a valid image file (jpg, jpeg, png, gif, webp)']);
            }

            // Delete old image
            if ($category->image) {
                $oldImagePath = public_path('storage/' . $category->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Create filename manually
            $filename = 'category_' . time() . '_' . uniqid() . '.' . $extension;

            // Move file to public/storage/categories (where storage link points)
            $destinationPath = public_path('storage/categories');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['image'] = 'categories/' . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->image) {
            $imagePath = public_path('storage/' . $category->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
