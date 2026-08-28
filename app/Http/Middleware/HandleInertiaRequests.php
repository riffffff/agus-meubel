<?php

namespace App\Http\Middleware;

use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shopSettings = ShopSetting::getSettings();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'shopSettings' => [
                'id' => $shopSettings->id,
                'shop_name' => $shopSettings->shop_name,
                'address' => $shopSettings->address,
                'whatsapp_number' => $shopSettings->whatsapp_number,
                'whatsapp_template' => $shopSettings->whatsapp_template,
                'operating_hours' => $shopSettings->operating_hours,
                'hero_banner_text_1' => $shopSettings->hero_banner_text_1,
                'hero_banner_text_2' => $shopSettings->hero_banner_text_2,
                'hero_banner_bg' => $shopSettings->hero_banner_bg,
                'shipping_areas' => $shopSettings->shipping_areas,
                'shipping_estimate_days' => $shopSettings->shipping_estimate_days,
            ],
        ];
    }
}
