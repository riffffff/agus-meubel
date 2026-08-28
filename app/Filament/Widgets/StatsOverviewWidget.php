<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Product;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Data Produk
        $totalProducts = Product::where('is_published', true)->count();
        $readyStock = Product::where('is_published', true)->where('stock_status', 'available')->count();
        $preOrder = Product::where('is_published', true)->where('stock_status', 'preorder')->count();
        $outOfStock = Product::where('is_published', true)->where('stock_status', 'out_of_stock')->count();

        // Data Artikel
        $totalArticles = Article::where('is_published', true)->count();
        $heroArticles = Article::where('is_published', true)->where('is_hero', true)->count();

        // Data Review
        $totalReviews = Review::count();
        $approvedReviews = Review::where('is_approved', true)->count();
        $pendingReviews = Review::where('is_approved', false)->count();
        $avgRating = Review::where('is_approved', true)->avg('rating') ?? 0;

        // Total Views
        $productViews = Product::sum('views_count');
        $articleViews = Article::sum('views_count');
        $totalViews = $productViews + $articleViews;

        return [
            Stat::make('Katalog Produk', $totalProducts)
                ->description("{$readyStock} Ready Stock")
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->chart([3, 5, 4, 6, $totalProducts])
                ->url(route('filament.admin.resources.products.index')),

            Stat::make('Artikel & Edukasi', $totalArticles)
                ->description("{$totalArticles} Artikel Terbit")
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('info')
                ->chart([2, 4, 3, 5, $totalArticles])
                ->url(route('filament.admin.resources.articles.index')),

            Stat::make('Review Pelanggan', $totalReviews)
                ->description($pendingReviews > 0 ? "{$pendingReviews} Perlu Moderasi" : "Rating " . number_format($avgRating, 1) . " ★")
                ->descriptionIcon('heroicon-m-star')
                ->color($pendingReviews > 0 ? 'warning' : 'success')
                ->chart([1, 3, 2, 4, $totalReviews])
                ->url(route('filament.admin.resources.reviews.index')),

            Stat::make('Total Kunjungan', number_format($totalViews, 0, ',', '.'))
                ->description('Halaman produk & artikel')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary')
                ->chart([10, 25, 45, 70, max($totalViews, 100)]),
        ];
    }
}
