<?php

namespace App\Providers;

use App\Models\ProductImage;
use App\Observers\ProductImageObserver;
use Illuminate\Config\Repository;
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

        $config = $this->app->make(Repository::class);
        $currentBase = rtrim(URL::to('/'), '/');
        $config->set('filesystems.disks.public.url', $currentBase . '/storage');

        ProductImage::observe(ProductImageObserver::class);
    }
}
