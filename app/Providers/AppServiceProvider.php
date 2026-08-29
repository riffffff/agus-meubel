<?php

namespace App\Providers;

use App\Models\ProductImage;
use App\Observers\ProductImageObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $isHttps =
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos((string)$_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) ||
            (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
            (isset($_SERVER['HTTP_CF_VISITOR']) && strpos((string)$_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false);

        if ($isHttps) {
            URL::forceScheme('https');
            if (isset($_SERVER)) {
                $_SERVER['HTTPS'] = 'on';
            }
        }

        ProductImage::observe(ProductImageObserver::class);

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Cart mutation: maks 30 request/menit per user
        RateLimiter::for('cart', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'rate_limit' => 'Terlalu banyak permintaan. Silakan tunggu sebentar.',
                    ]);
                });
        });

        // Halaman publik (produk & artikel): maks 120 request/menit per IP
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Submit review publik: maks 3 review per 24 jam per IP
        RateLimiter::for('review', function (Request $request) {
            return Limit::perDay(3)
                ->by($request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'rate_limit' => 'Anda telah mencapai batas pengiriman ulasan hari ini. Silakan coba lagi besok.',
                    ]);
                });
        });
    }
}
