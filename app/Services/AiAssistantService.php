<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;

class AiAssistantService
{
    public function handle(string $message): array
    {
        $filters = $this->extractFilters($message);
        $products = $this->searchProducts($filters);
        $fallbackProducts = collect();

        // If strict filters return nothing, relax category/color once.
        if ($products->count() === 0) {
            $relaxed = $filters;
            $relaxed['category'] = null;
            $relaxed['color'] = null;
            $fallbackProducts = $this->searchProducts($relaxed);
        }

        $finalProducts = $products->count() ? $products : $fallbackProducts;

        $reply = $this->buildReply($message, $filters, $finalProducts);
        $followUps = $this->buildFollowUps($filters, $finalProducts->count());

        return [
            'reply' => $reply,
            'filters' => $filters,
            'follow_ups' => $followUps,
            'products' => $finalProducts->map(function (Product $p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (float) $p->current_price,
                    'slug' => $p->slug,
                    'image_url' => $p->image_url,
                ];
            })->values(),
        ];
    }

    protected function extractFilters(string $message): array
    {
        $text = mb_strtolower(trim($message), 'UTF-8');
        $normalized = $this->normalizeText($text);
        $filters = [
            'category' => null,
            'price_min' => null,
            'price_max' => null,
            'color' => null,
            'size' => null,
            'keywords' => [],
        ];

        $categoryMap = [
            'dress' => 'womens-fashion',
            'فستان' => 'womens-fashion',
            'women' => 'womens-fashion',
            'رجالي' => 'mens-fashion',
            'men' => 'mens-fashion',
            'shoe' => 'shoes',
            'shoes' => 'shoes',
            'حذاء' => 'shoes',
            'bag' => 'bags',
            'شنطة' => 'bags',
            'accessories' => 'accessories',
            'اكسسوار' => 'accessories',
        ];

        foreach ($categoryMap as $keyword => $slug) {
            if (mb_strpos($text, $keyword) !== false) {
                $filters['category'] = $slug;
                break;
            }
        }

        $colorMap = [
            'black' => 'black', 'اسود' => 'black',
            'white' => 'white', 'ابيض' => 'white',
            'red' => 'red', 'احمر' => 'red',
            'blue' => 'blue', 'ازرق' => 'blue',
            'green' => 'green', 'اخضر' => 'green',
        ];
        foreach ($colorMap as $word => $color) {
            if (mb_strpos($text, $word) !== false) {
                $filters['color'] = $color;
                break;
            }
        }

        if (preg_match('/(?:under|اقل من|تحت)\s*(\d+)/u', $text, $m)) {
            $filters['price_max'] = (float) $m[1];
        }
        if (preg_match('/(?:over|اكتر من|فوق)\s*(\d+)/u', $text, $m)) {
            $filters['price_min'] = (float) $m[1];
        }
        if (preg_match('/(?:size|مقاس)\s*(xs|s|m|l|xl|xxl|\d{2})/u', $text, $m)) {
            $filters['size'] = strtoupper((string) $m[1]);
        }

        // Add full message as a keyword phrase to support direct single-phrase matching.
        if (mb_strlen($normalized, 'UTF-8') >= 2) {
            $filters['keywords'][] = $normalized;
        }

        foreach (preg_split('/\s+/u', $normalized) as $token) {
            if (mb_strlen($token, 'UTF-8') >= 2) {
                $filters['keywords'][] = $token;
            }
        }
        $filters['keywords'] = array_values(array_unique($filters['keywords']));

        return $filters;
    }

    protected function searchProducts(array $filters)
    {
        $q = Product::query()->where('is_active', true)->with('category');

        if (!empty($filters['category'])) {
            $slug = $filters['category'];
            $q->whereHas('category', fn($c) => $c->where('slug', $slug));
        }
        if ($filters['price_min'] !== null) {
            $q->where('price', '>=', $filters['price_min']);
        }
        if ($filters['price_max'] !== null) {
            $q->where('price', '<=', $filters['price_max']);
        }
        if (!empty($filters['size'])) {
            $size = $filters['size'];
            $q->where('sizes', 'like', '%"'.$size.'"%');
        }
        if (!empty($filters['color'])) {
            $color = $filters['color'];
            $q->where(function ($inner) use ($color) {
                $inner->where('colors', 'like', '%'.$color.'%')
                    ->orWhere('name', 'like', '%'.$color.'%')
                    ->orWhere('description', 'like', '%'.$color.'%');
            });
        }

        if (!empty($filters['keywords'])) {
            $keywords = $this->expandKeywordVariants($filters['keywords']);
            $q->where(function ($inner) use ($keywords) {
                foreach ($keywords as $kw) {
                    $inner->orWhere('name', 'like', "%{$kw}%")
                        ->orWhere('description', 'like', "%{$kw}%")
                        ->orWhereJsonContains('ai_keywords', $kw);
                }
            });
        }
        $results = $q->orderByDesc('created_at')->limit(8)->get();

        // Final fallback: fuzzy match against saved keyword arrays in PHP.
        if ($results->isEmpty() && !empty($filters['keywords'])) {
            $keywords = $this->expandKeywordVariants($filters['keywords']);
            $candidates = Product::query()
                ->where('is_active', true)
                ->whereNotNull('ai_keywords')
                ->orderByDesc('created_at')
                ->limit(300)
                ->get();

            $results = $candidates->filter(function (Product $p) use ($keywords) {
                $joined = $this->normalizeText(implode(' ', $p->ai_keywords ?? []));
                foreach ($keywords as $kw) {
                    if ($kw !== '' && mb_strpos($joined, $kw) !== false) {
                        return true;
                    }
                }
                return false;
            })->take(8)->values();
        }

        return $results;
    }

    protected function buildReply(string $message, array $filters, $products): string
    {
        $aiText = $this->askExternalAi($message, $products->pluck('name')->all());
        if ($aiText) {
            return $aiText;
        }

        if ($products->count() === 0) {
            return 'ما لقيت نتائج مطابقة الآن. جرّب تغيّر الفئة أو السعر أو اللون.';
        }

        $parts = [];
        if (!empty($filters['category'])) $parts[] = 'الفئة';
        if ($filters['price_max'] !== null) $parts[] = 'حد السعر';
        if (!empty($filters['color'])) $parts[] = 'اللون';
        if (!empty($filters['size'])) $parts[] = 'المقاس';

        $hint = empty($parts) ? 'طلبك العام' : implode(' + ', $parts);
        return 'لقيت '.$products->count().' منتجات مناسبة بناءً على '.$hint.'.';
    }

    protected function buildFollowUps(array $filters, int $count): array
    {
        $actions = [];

        if ($count > 0) {
            $actions[] = 'اعرض الأرخص';
            $actions[] = 'أريد نفس المنتجات بألوان أخرى';
            $actions[] = 'اعرض منتجات أقل من 50';
        } else {
            $actions[] = 'جرّب بدون تحديد اللون';
            $actions[] = 'اعرض كل المنتجات الجديدة';
            $actions[] = 'اعرض منتجات ضمن ميزانية 100';
        }

        if (empty($filters['size'])) {
            $actions[] = 'اعرض مقاس M';
        }
        if (empty($filters['category'])) {
            $actions[] = 'اعرض فساتين';
        }

        return array_values(array_unique(array_slice($actions, 0, 4)));
    }

    protected function askExternalAi(string $message, array $productNames): ?string
    {
        $key = (string) env('AI_API_KEY', '');
        $url = (string) env('AI_API_URL', '');
        $model = (string) env('AI_MODEL', 'gpt-4o-mini');

        if ($key === '' || $url === '') {
            return null;
        }

        try {
            $context = empty($productNames) ? 'No product names found.' : implode(', ', $productNames);
            $prompt = "User request: {$message}\nMatching products: {$context}\nReply shortly in Arabic with helpful shopping guidance.";

            $res = Http::timeout(12)
                ->withToken($key)
                ->post($url, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a shopping assistant for an ecommerce store.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.4,
                ]);

            if (!$res->ok()) {
                return null;
            }

            $json = $res->json();
            return $json['choices'][0]['message']['content'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function normalizeText(string $text): string
    {
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    }

    protected function expandKeywordVariants(array $keywords): array
    {
        $variants = [];

        foreach ($keywords as $kw) {
            $kw = $this->normalizeText((string) $kw);
            if ($kw === '') {
                continue;
            }

            $variants[] = $kw;

            // Normalize common Arabic letter alternatives.
            $normalizedArabic = strtr($kw, [
                'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا',
                'ة' => 'ه', 'ى' => 'ي', 'ؤ' => 'و', 'ئ' => 'ي',
            ]);
            $variants[] = $normalizedArabic;

            // Helps with minor ending typos (e.g. شنطة vs شنتا).
            if (mb_strlen($kw, 'UTF-8') >= 4) {
                $variants[] = mb_substr($kw, 0, mb_strlen($kw, 'UTF-8') - 1, 'UTF-8');
            }
        }

        $variants = array_filter(array_map(fn($v) => trim((string) $v), $variants));
        return array_values(array_unique($variants));
    }
}
