<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticleController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Articles/Form', [
            'isEdit' => false,
            'article' => null
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:255',
            'image' => 'required|image|max:5120', // max 5MB
            'is_hero' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $isHero = $request->boolean('is_hero');

        // Business Rule validation: Max 3 hero articles
        if ($isHero) {
            $heroCount = Article::where('is_hero', true)->count();
            if ($heroCount >= 3) {
                throw ValidationException::withMessages([
                    'is_hero' => 'Maksimal kuota artikel hero aktif dibatasi ketat hanya 3 artikel.'
                ]);
            }
        }

        // Process image
        $imagePath = $this->imageService->process($request->file('image'), 'articles');

        Article::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5), // ensure uniqueness
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'image' => $imagePath,
            'is_hero' => $isHero,
            'published_at' => $request->published_at ?? now(),
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article)
    {
        return Inertia::render('Admin/Articles/Form', [
            'isEdit' => true,
            'article' => $article
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
            'is_hero' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $isHero = $request->boolean('is_hero');

        // Business Rule validation: Max 3 hero articles
        if ($isHero && !$article->is_hero) {
            $heroCount = Article::where('is_hero', true)->count();
            if ($heroCount >= 3) {
                throw ValidationException::withMessages([
                    'is_hero' => 'Maksimal kuota artikel hero aktif dibatasi ketat hanya 3 artikel.'
                ]);
            }
        }

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'is_hero' => $isHero,
            'published_at' => $request->published_at ?? $article->published_at,
        ];

        // Process new image if uploaded
        if ($request->hasFile('image')) {
            // Delete old image
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $this->imageService->process($request->file('image'), 'articles');
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        // Delete image file
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
