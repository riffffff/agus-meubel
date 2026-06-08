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
        }]);

        // Filter stock status
        if ($request->filled('stock_status')) {
            $query->where('stock_status', $request->stock_status);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        if ($sort === 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_high') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();
        $shopSettings = ShopSetting::first();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'shopSettings' => $shopSettings,
            'filters' => $request->only(['stock_status', 'sort'])
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with('images')->where('slug', $slug)->firstOrFail();
        $shopSettings = ShopSetting::first();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'shopSettings' => $shopSettings
        ]);
    }
}
