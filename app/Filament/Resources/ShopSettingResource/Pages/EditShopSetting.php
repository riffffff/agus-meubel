<?php

namespace App\Filament\Resources\ShopSettingResource\Pages;

use App\Filament\Resources\ShopSettingResource;
use App\Models\ShopSetting;
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
