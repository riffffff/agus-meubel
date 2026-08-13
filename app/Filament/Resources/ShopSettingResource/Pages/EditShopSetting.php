<?php

namespace App\Filament\Resources\ShopSettingResource\Pages;

use App\Filament\Resources\ShopSettingResource;
use App\Models\ShopSetting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
}
