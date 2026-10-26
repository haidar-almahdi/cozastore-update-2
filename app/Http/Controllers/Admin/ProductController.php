<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'ai_keywords' => 'nullable|string|max:2000',
                'price' => 'required|numeric|min:0',
                'discount_percentage' => 'nullable|integer|min:0|max:90',
                'discount_starts_at' => 'nullable|date',
                'discount_ends_at' => 'nullable|date|after_or_equal:discount_starts_at',
                'black_friday_enabled' => 'nullable|boolean',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image' => 'required|file',
                'additional_images.*' => 'nullable|file',
                'sizes' => 'nullable|array',
                'sizes.*' => 'nullable|string',
                'size_stocks' => 'nullable|array',
                'color_codes' => 'nullable|array',
                'color_codes.*' => 'nullable|string',
                'color_names' => 'nullable|array',
                'color_names.*' => 'nullable|string',
            ]);

            $data = [
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'description' => $request->description,
                'ai_keywords' => $this->parseAiKeywords($request->input('ai_keywords')),
                'price' => $request->price,
                'discount_percentage' => (int) ($request->discount_percentage ?? 0),
                'discount_starts_at' => $request->discount_starts_at ?: null,
                'discount_ends_at' => $request->discount_ends_at ?: null,
                'black_friday_enabled' => $request->boolean('black_friday_enabled', true),
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'is_active' => true,
                'sizes' => $request->sizes ?? [],
                'colors' => $this->processColors($request),
                'size_stocks' => $this->normalizeSizeStocks($request->sizes ?? [], $request->size_stocks ?? []),
            ];

            if (!empty($data['sizes'])) {
                $data['stock'] = array_sum($data['size_stocks']);
            }

            // رفع الصورة الرئيسية
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'));
            }

            // رفع الصور الإضافية
            if ($request->hasFile('additional_images')) {
                $additionalImages = [];
                foreach ($request->file('additional_images') as $file) {
                    $additionalImages[] = $this->uploadImage($file);
                }
                $data['additional_images'] = $additionalImages;
            }

            Product::create($data);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error creating product: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'ai_keywords' => 'nullable|string|max:2000',
                'price' => 'required|numeric|min:0',
                'discount_percentage' => 'nullable|integer|min:0|max:90',
                'discount_starts_at' => 'nullable|date',
                'discount_ends_at' => 'nullable|date|after_or_equal:discount_starts_at',
                'black_friday_enabled' => 'nullable|boolean',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|file',
                'additional_images.*' => 'nullable|file',
                'sizes' => 'nullable|array',
                'sizes.*' => 'nullable|string',
                'size_stocks' => 'nullable|array',
                'color_codes' => 'nullable|array',
                'color_codes.*' => 'nullable|string',
                'color_names' => 'nullable|array',
                'color_names.*' => 'nullable|string',
            ]);

            $data = [
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'description' => $request->description,
                'ai_keywords' => $this->parseAiKeywords($request->input('ai_keywords')),
                'price' => $request->price,
                'discount_percentage' => (int) ($request->discount_percentage ?? 0),
                'discount_starts_at' => $request->discount_starts_at ?: null,
                'discount_ends_at' => $request->discount_ends_at ?: null,
                'black_friday_enabled' => $request->boolean('black_friday_enabled', true),
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'sizes' => $request->sizes ?? [],
                'colors' => $this->processColors($request),
                'size_stocks' => $this->normalizeSizeStocks($request->sizes ?? [], $request->size_stocks ?? []),
            ];

            if (!empty($data['sizes'])) {
                $data['stock'] = array_sum($data['size_stocks']);
            }

            // رفع الصورة الرئيسية الجديدة
            if ($request->hasFile('image')) {
                // حذف الصورة القديمة
                if ($product->image) {
                    $oldImagePath = public_path('storage/' . $product->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $data['image'] = $this->uploadImage($request->file('image'));
            }

            // رفع الصور الإضافية الجديدة
            if ($request->hasFile('additional_images')) {
                // حذف الصور الإضافية القديمة
                if ($product->additional_images) {
                    foreach ($product->additional_images as $oldImage) {
                        $oldImagePath = public_path('storage/' . $oldImage);
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                }

                $additionalImages = [];
                foreach ($request->file('additional_images') as $file) {
                    $additionalImages[] = $this->uploadImage($file);
                }
                $data['additional_images'] = $additionalImages;
            }

            $product->update($data);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating product: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        // حذف الصور
        if ($product->image) {
            $imagePath = public_path('storage/' . $product->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($product->additional_images) {
            foreach ($product->additional_images as $image) {
                $imagePath = public_path('storage/' . $image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function uploadImage($file)
    {
        // التحقق من نوع الملف بطريقة بديلة بدون fileinfo
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $originalName = $file->getClientOriginalName();
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // إذا لم نحصل على extension، نستخدم jpg كافتراضي
        if (empty($extension) || !in_array($extension, $allowedExtensions)) {
            $extension = 'jpg';
        }

        $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;

        $destinationPath = public_path('storage/products');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        try {
            $file->move($destinationPath, $filename);
        } catch (\Exception $e) {
            throw new \Exception('Failed to upload image: ' . $e->getMessage());
        }

        return 'products/' . $filename;
    }

    private function processColors($request)
    {
        $colors = [];

        if ($request->has('color_codes') && $request->has('color_names')) {
            $colorCodes = $request->color_codes ?? [];
            $colorNames = $request->color_names ?? [];

            foreach ($colorCodes as $index => $colorCode) {
                if (!empty($colorCode)) {
                    $colors[] = [
                        'name' => $colorNames[$index] ?? $this->getColorName($colorCode),
                        'code' => $colorCode
                    ];
                }
            }
        }

        return $colors;
    }

    private function getColorName($colorCode)
    {
        $colorNames = [
            '#000000' => 'Black',
            '#FFFFFF' => 'White',
            '#FF0000' => 'Red',
            '#00FF00' => 'Green',
            '#0000FF' => 'Blue',
            '#FFFF00' => 'Yellow',
            '#FF00FF' => 'Magenta',
            '#00FFFF' => 'Cyan',
            '#FFA500' => 'Orange',
            '#800080' => 'Purple',
            '#FFC0CB' => 'Pink',
            '#A52A2A' => 'Brown',
            '#808080' => 'Gray',
            '#000080' => 'Navy',
            '#008000' => 'Dark Green',
            '#800000' => 'Maroon',
            '#808000' => 'Olive',
            '#008080' => 'Teal'
        ];

        return $colorNames[strtoupper($colorCode)] ?? 'Custom Color';
    }

    private function parseAiKeywords(?string $raw): array
    {
        if (!$raw) {
            return [];
        }

        $raw = str_replace(["\r\n", "\r", "،", ";", "|"], "\n", $raw);
        $parts = preg_split('/[\n,]+/u', $raw) ?: [];
        $keywords = [];

        foreach ($parts as $part) {
            $kw = trim((string) $part);
            if ($kw === '') {
                continue;
            }
            if (mb_strlen($kw, 'UTF-8') < 2) {
                continue;
            }
            $keywords[] = mb_strtolower($kw, 'UTF-8');
        }

        return array_values(array_unique($keywords));
    }

    private function normalizeSizeStocks(array $sizes, array $sizeStocks): array
    {
        $normalized = [];
        foreach ($sizes as $size) {
            $key = (string) $size;
            $normalized[$key] = max(0, (int) ($sizeStocks[$key] ?? 0));
        }

        return $normalized;
    }
}
