<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use App\Services\ImageService;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['price']) && is_string($data['price'])) {
            $data['price'] = (int) preg_replace('/[^0-9]/', '', $data['price']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $rawState = method_exists($this->form, 'getRawState') ? $this->form->getRawState() : [];
        $newImages = $this->data['new_images']
            ?? ($rawState['new_images'] ?? [])
            ?? ($this->form->getState()['new_images'] ?? []);

        if (!is_array($newImages)) {
            $newImages = [];
        }

        $newImages = array_values(array_filter($newImages));
        if (count($newImages) === 0) {
            return;
        }

        /** @var ImageService $imageService */
        $imageService = app(ImageService::class);

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
            ProductImage::create([
                'product_id' => $this->record->id,
                'url'        => ltrim($url, '/'),
                'is_primary' => $idx === 0,
                'sort_order' => $idx,
            ]);
        }
    }
}
