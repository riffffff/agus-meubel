<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Widgets\Widget;

class LatestReviewsWidget extends Widget
{
    protected static string $view = 'filament.widgets.latest-reviews-widget';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'md' => 'full',
        'lg' => 1,
    ];

    protected function getViewData(): array
    {
        $reviews = Review::with('product')
            ->latest()
            ->limit(4)
            ->get();

        return compact('reviews');
    }

    public function approveReview(int $id): void
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);
    }
}
