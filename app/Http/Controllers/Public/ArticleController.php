<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ShopSetting;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::published()->paginate(9);
        $shopSettings = ShopSetting::getSettings();

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'shopSettings' => $shopSettings,
        ]);
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Track view count
        $article->incrementView();

        $shopSettings = ShopSetting::getSettings();

        return Inertia::render('Articles/Show', [
            'article' => $article,
            'shopSettings' => $shopSettings,
        ]);
    }
}
