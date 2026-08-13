<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController as PublicProductController;
use App\Http\Controllers\Public\ArticleController as PublicArticleController;
use App\Http\Controllers\ProfileController;

// Public Routes
Route::get('/', HomeController::class)->name('home');
Route::get('/produk', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [PublicProductController::class, 'show'])->name('products.show');
Route::get('/artikel', [PublicArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{slug}', [PublicArticleController::class, 'show'])->name('articles.show');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/admin');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/item/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.item.update');
    Route::delete('/cart/item/{cartItem}', [CartController::class, 'destroy'])->name('cart.item.destroy');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});

require __DIR__.'/auth.php';
