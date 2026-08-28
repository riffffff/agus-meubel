<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\Widget;

class TopProductsWidget extends Widget
{
    protected static string $view = 'filament.widgets.top-products-widget';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 2,
    ];

    protected function getViewData(): array
    {
        $products = Product::where('is_published', true)
            ->with('primaryImage')
            ->orderByDesc('views_count')
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();

        return compact('products');
    }
}
