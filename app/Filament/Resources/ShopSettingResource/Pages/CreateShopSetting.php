<?php

namespace App\Filament\Resources\ShopSettingResource\Pages;

use App\Filament\Resources\ShopSettingResource;
use App\Models\ShopSetting;
use App\Services\ImageService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateShopSetting extends CreateRecord
{
    protected static string $resource = ShopSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var ImageService $imageService */
        $imageService = app(ImageService::class);

        foreach (['logo', 'logo_dark', 'favicon', 'hero_banner_bg'] as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = $imageService->processUploadedPath($data[$field], 'branding') ?? $data[$field];
            }
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $existing = ShopSetting::query()->find(ShopSetting::SINGLETON_ID);

        if ($existing) {
            $existing->fill($data);
            $existing->save();
            Cache::forget(ShopSetting::CACHE_KEY);
            return $existing;
        }

        $record = new ShopSetting();
        $record->fill($data);
        $record->id = ShopSetting::SINGLETON_ID;
        $record->save();
        Cache::forget(ShopSetting::CACHE_KEY);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return ShopSettingResource::getUrl('edit', ['record' => ShopSetting::SINGLETON_ID]);
    }

    protected function afterSave(): void
    {
        Cache::forget(ShopSetting::CACHE_KEY);
    }
}
