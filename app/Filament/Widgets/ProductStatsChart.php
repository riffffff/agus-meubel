<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\Widget;

class ProductStatsChart extends Widget
{
    protected static string $view = 'filament.widgets.product-stats-chart';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 1,
    ];

    protected function getViewData(): array
    {
        $available   = Product::where('is_published', true)->where('stock_status', 'available')->count();
        $preorder    = Product::where('is_published', true)->where('stock_status', 'preorder')->count();
        $outOfStock  = Product::where('is_published', true)->where('stock_status', 'out_of_stock')->count();
        $draft       = Product::where('is_published', false)->count();
        $total       = $available + $preorder + $outOfStock + $draft;

        return compact('available', 'preorder', 'outOfStock', 'draft', 'total');
    }
}
