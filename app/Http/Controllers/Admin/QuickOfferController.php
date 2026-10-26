<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class QuickOfferController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(12);
        return view('admin.offers.index', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'quick_offer_enabled' => 'nullable|boolean',
            'quick_offer_percentage' => 'required|integer|min:0|max:90',
            'quick_offer_starts_at' => 'nullable|date',
            'quick_offer_ends_at' => 'nullable|date|after_or_equal:quick_offer_starts_at',
        ]);

        $percentage = (int) ($data['quick_offer_percentage'] ?? 0);
        // Make quick offers predictable: any percentage > 0 becomes active by default.
        $enabled = $percentage > 0 ? true : $request->boolean('quick_offer_enabled');

        $product->update([
            'quick_offer_enabled' => $enabled,
            'quick_offer_percentage' => $percentage,
            'quick_offer_starts_at' => $data['quick_offer_starts_at'] ?: null,
            'quick_offer_ends_at' => $data['quick_offer_ends_at'] ?: null,
        ]);

        return back()->with('success', "Quick offer updated for {$product->name}.");
    }
}
