<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $newImages = $this->data['new_images'] ?? [];

        if (!is_array($newImages)) {
            $newImages = [];
        }

        $newImages = array_values(array_filter($newImages));
        if (count($newImages) === 0) {
            return;
        }

        $currentCount = ProductImage::where('product_id', $this->record->id)->count();
        $hasPrimary = ProductImage::where('product_id', $this->record->id)
            ->where('is_primary', true)
            ->exists();

        foreach ($newImages as $idx => $url) {
            if (empty($url)) {
                continue;
            }
            $sortOrder = $currentCount + $idx;
            ProductImage::create([
                'product_id' => $this->record->id,
                'url'        => ltrim($url, '/'),
                'is_primary' => !$hasPrimary && $idx === 0,
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
