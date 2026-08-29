<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon & Global Shop Settings -->
        @php
            try {
                $shopSettings = \App\Models\ShopSetting::getSettings();
                $faviconUrl   = $shopSettings->getFaviconUrl();
                $logoLightUrl = $shopSettings->getLogoUrl('light');
                $logoDarkUrl  = $shopSettings->getLogoUrl('dark');
                $__shopGlobal = [
                  'shop_name'     => $shopSettings->shop_name,
                  'logo_url'      => $logoLightUrl,
                  'logo_dark_url' => $logoDarkUrl,
                  'favicon_url'   => $faviconUrl,
                ];
            } catch (\Throwable) {
                $faviconUrl = null;
                $logoLightUrl = null;
                $logoDarkUrl = null;
                $__shopGlobal = null;
            }
        @endphp
        @if ($__shopGlobal)
        <script>
            window.__SHOP__ = @json($__shopGlobal);
        </script>
        @endif
        @if ($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
