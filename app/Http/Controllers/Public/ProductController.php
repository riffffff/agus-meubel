<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['images' => function ($query) {
            $query->where('is_primary', true);
        }])->where('is_published', true);

        if ($request->filled('stock_status')) {
            $allowedStatus = ['available', 'preorder', 'out_of_stock'];
            if (in_array($request->stock_status, $allowedStatus, true)) {
                $query->where('stock_status', $request->stock_status);
            }
        }

        $sort = $request->input('sort', 'newest');
        if ($sort === 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_high') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();
        $shopSettings = ShopSetting::getSettings();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'shopSettings' => $shopSettings,
            'filters' => $request->only(['stock_status', 'sort'])
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with('images')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Track view count
        $product->incrementView();

        $shopSettings = ShopSetting::getSettings();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'shopSettings' => $shopSettings
        ]);
    }
}
