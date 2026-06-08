<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('published_at', 'desc')
            ->paginate(9);

        return Inertia::render('Articles/Index', [
            'articles' => $articles
        ]);
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        return Inertia::render('Articles/Show', [
            'article' => $article
        ]);
    }
}
