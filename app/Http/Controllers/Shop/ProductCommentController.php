<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use App\Services\LoyaltyService;

class ProductCommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = ProductComment::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'content' => $validated['content'],
        ]);

        /** @var LoyaltyService $loyalty */
        $loyalty = app(LoyaltyService::class);
        $result = $loyalty->awardCommentPointIfEligible($request->user(), $product);

        $message = 'Comment added successfully.';
        if ($result['reason'] === 'purchase_required') {
            $message .= ' Loyalty point was not added (available only after purchasing this product).';
        } elseif ($result['reason'] === 'already_awarded') {
            $message .= ' Loyalty point already granted before for this product.';
        } elseif ($result['reason'] === 'awarded') {
            $message .= ' +1 loyalty point added.';
        }

        return back()->with('status', $message);
    }
}
