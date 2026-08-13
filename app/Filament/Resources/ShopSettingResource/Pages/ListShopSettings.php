<?php

namespace App\Filament\Resources\ShopSettingResource\Pages;

use App\Filament\Resources\ShopSettingResource;
use App\Models\ShopSetting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListShopSettings extends ListRecords
{
    protected static string $resource = ShopSettingResource::class;

    protected function getHeaderActions(): array
    {
        $settingsExist = ShopSetting::query()->exists();

        return [
            $settingsExist
                ? Actions\Action::make('edit_settings')
                    ->label('Edit Pengaturan')
                    ->icon('heroicon-o-pencil')
                    ->url(fn () => ShopSettingResource::getUrl('edit', ['record' => ShopSetting::SINGLETON_ID]))
                    ->color('primary')
                : Actions\CreateAction::make()
                    ->label('Buat Pengaturan')
                    ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->orderBy('id', 'asc');
    }
}
