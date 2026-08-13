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
        $heroArticles = Article::hero()->get();

        $topProducts = Product::with(['images' => function ($query) {
                $query->where('is_primary', true);
            }])
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $reviews = Review::where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->take(9)
            ->get();

        $shopSettings = ShopSetting::getSettings();

        return Inertia::render('Home', [
            'heroArticles' => $heroArticles,
            'topProducts' => $topProducts,
            'reviews' => $reviews,
            'shopSettings' => $shopSettings,
        ]);
    }
}
