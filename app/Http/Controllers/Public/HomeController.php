<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShopSetting;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        // Get hero articles (max 3)
        $heroArticles = Article::heroArticles()
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // Get top 8 products with primary images
        $topProducts = Product::with(['images' => function ($query) {
                $query->where('is_primary', true);
            }])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Get reviews
        $reviews = Review::orderBy('created_at', 'desc')->take(10)->get();

        // Get shop settings
        $shopSettings = ShopSetting::first();

        return Inertia::render('Home', [
            'heroArticles' => $heroArticles,
            'topProducts' => $topProducts,
            'reviews' => $reviews,
            'shopSettings' => $shopSettings,
        ]);
    }
}
