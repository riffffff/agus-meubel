<?php

namespace App\Observers;

use App\Models\ShopSetting;
use App\Services\ImageService;

class ShopSettingObserver
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    public function saved(ShopSetting $setting): void
    {
        if (!$setting->wasRecentlyCreated) {
            $this->cleanupOldImages($setting);
        }

        rescue(function () use ($setting) {
            cache()->forget('shop_settings');
            cache()->forget('shop_settings:data');
        });
    }

    private function cleanupOldImages(ShopSetting $setting): void
    {
        $fields = ['logo', 'logo_dark', 'favicon'];

        foreach ($fields as $field) {
            if (!$setting->isDirty($field)) {
                continue;
            }

            $oldValue = $setting->getOriginal($field);
            $newValue = $setting->getAttribute($field);

            if (
                !empty($oldValue)
                && $oldValue !== $newValue
            ) {
                try {
                    $this->imageService->deleteIfExists($oldValue);
                } catch (\Throwable) {
                }
            }
        }
    }
}
