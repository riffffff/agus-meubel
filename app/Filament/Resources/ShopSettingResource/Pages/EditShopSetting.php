<?php

namespace App\Filament\Resources\ShopSettingResource\Pages;

use App\Filament\Resources\ShopSettingResource;
use App\Models\ShopSetting;
use App\Services\ImageService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditShopSetting extends EditRecord
{
    protected static string $resource = ShopSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_list')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => ShopSettingResource::getUrl('index')),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getRedirectUrl(): string
    {
        return ShopSettingResource::getUrl('edit', ['record' => $this->record]);
    }

    protected function afterSave(): void
    {
        // Bersihkan cache setelah admin menyimpan perubahan
        Cache::forget(ShopSetting::CACHE_KEY);
    }
}
