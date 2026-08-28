<?php

namespace App\Filament\Resources\ShopSettingResource\Pages;

use App\Filament\Resources\ShopSettingResource;
use App\Models\ShopSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateShopSetting extends CreateRecord
{
    protected static string $resource = ShopSettingResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $existing = ShopSetting::query()->find(ShopSetting::SINGLETON_ID);

        if ($existing) {
            $existing->fill($data);
            $existing->save();
            return $existing;
        }

        $record = new ShopSetting();
        $record->fill($data);
        $record->id = ShopSetting::SINGLETON_ID;
        $record->save();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return ShopSettingResource::getUrl('edit', ['record' => ShopSetting::SINGLETON_ID]);
    }
}
