<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SizeGuide;
use Illuminate\Http\Request;

class SizeGuideController extends Controller
{
    public function show(Product $product, Request $request)
    {
        $guide = SizeGuide::where('scope_type', 'product')
            ->where('scope_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$guide && $product->category_id) {
            $guide = SizeGuide::where('scope_type', 'category')
                ->where('scope_id', $product->category_id)
                ->where('is_active', true)
                ->first();
        }

        return view('shop.size-guide.show', [
            'product' => $product,
            'guide' => $guide,
            'unit' => $request->query('unit', $guide->unit ?? 'cm'),
        ]);
    }

    public function sizeInfo(Product $product, Request $request)
    {
        $size = trim((string) $request->query('size', ''));
        if ($size === '') {
            return response()->json([
                'error' => 'Missing required parameter: size'
            ], 422);
        }

        $guide = SizeGuide::where('scope_type', 'product')
            ->where('scope_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (!$guide && $product->category_id) {
            $guide = SizeGuide::where('scope_type', 'category')
                ->where('scope_id', $product->category_id)
                ->where('is_active', true)
                ->first();
        }

        if (!$guide) {
            return response()->json([
                'found' => false,
                'size' => $size,
                'measurements' => [],
                'unit' => null,
            ]);
        }

        $schema = $guide->table_schema ?? [];
        $rows = $guide->rows ?? [];

        $sizeKeys = ['size', 'eu', 'us', 'uk'];
        $match = null;

        foreach ($rows as $r) {
            foreach ($sizeKeys as $k) {
                if (isset($r[$k]) && strcasecmp((string) $r[$k], $size) === 0) {
                    $match = $r;
                    break 2;
                }
            }

            if (!$match && !empty($schema)) {
                $firstKey = $schema[0]['key'] ?? null;
                if ($firstKey && isset($r[$firstKey]) && strcasecmp((string) $r[$firstKey], $size) === 0) {
                    $match = $r;
                    break;
                }
            }
        }

        if (!$match) {
            return response()->json([
                'found' => false,
                'size' => $size,
                'measurements' => [],
                'unit' => $guide->unit ?? null,
            ]);
        }

        return response()->json([
            'found' => true,
            'size' => $size,
            'measurements' => $match,
            'unit' => $guide->unit ?? null,
        ]);
    }
}
