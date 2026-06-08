<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\Public\ArticleController as PublicArticleController;

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ShopSettingController as AdminShopSettingController;

// Public Routes
Route::get('/', HomeController::class)->name('home');
Route::get('/produk', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [PublicProductController::class, 'show'])->name('products.show');
Route::get('/artikel', [PublicArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{slug}', [PublicArticleController::class, 'show'])->name('articles.show');

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Resources
    Route::resource('articles', AdminArticleController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('reviews', AdminReviewController::class);

    // Shop Settings (Singleton CRUD-ish)
    Route::get('settings', [AdminShopSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [AdminShopSettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
