<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use App\Services\ImageService;
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
        $formState = $this->form->getState();
        $newImages = $formState['new_images'] ?? [];

        if (!is_array($newImages)) {
            $newImages = [];
        }

        $newImages = array_values(array_filter($newImages));
        if (count($newImages) === 0) {
            return;
        }

        /** @var ImageService $imageService */
        $imageService = app(ImageService::class);

        $currentCount = ProductImage::where('product_id', $this->record->id)->count();
        $hasPrimary = ProductImage::where('product_id', $this->record->id)
            ->where('is_primary', true)
            ->exists();

        foreach ($newImages as $idx => $url) {
            if (empty($url)) {
                continue;
            }
            if (is_string($url)) {
                $processed = $imageService->processUploadedPath($url, 'products');
                if (!empty($processed)) {
                    $url = $processed;
                }
            }
            $sortOrder = $currentCount + $idx;
            ProductImage::create([
                'product_id' => $this->record->id,
                'url'        => ltrim($url, '/'),
                'is_primary' => !$hasPrimary && $idx === 0,
                'sort_order' => $sortOrder,
            ]);
        }

        $this->form->fill([
            ...$this->form->getState(),
            'new_images' => [],
        ]);
    }
}
