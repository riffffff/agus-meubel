<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $newImages = $this->data['new_images'] ?? [];

        if (!is_array($newImages)) {
            $newImages = [];
        }

        $newImages = array_values(array_filter($newImages));
        if (count($newImages) === 0) {
            return;
        }

        foreach ($newImages as $idx => $url) {
            if (empty($url)) {
                continue;
            }
            ProductImage::create([
                'product_id' => $this->record->id,
                'url'        => ltrim($url, '/'),
                'is_primary' => $idx === 0,
                'sort_order' => $idx,
            ]);
        }
    }
}
