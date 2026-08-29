<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$basePath = dirname(__DIR__);

/**
 * AUTO-DETECT LOKASI PUBLIC PATH untuk shared hosting.
 * Struktur hosting Aeron Host Anda:
 *   /home/user/
 *     ├── agus-meubel/    ← Laravel (basePath)
 *     └── public_html/    ← DocumentRoot web (tempat index.php sesungguhan)
 */
$detectPublicPath = function () use ($basePath): ?string {
    // 1. Prioritas 1: Override eksplisit dari .env
    if (!empty($_ENV['APP_PUBLIC_PATH']) && is_dir($_ENV['APP_PUBLIC_PATH'])) {
        return $_ENV['APP_PUBLIC_PATH'];
    }
    if (!empty($GLOBALS['APP_PUBLIC_PATH']) && is_dir($GLOBALS['APP_PUBLIC_PATH'])) {
        return $GLOBALS['APP_PUBLIC_PATH'];
    }

    // 2. Cek apakah public/ di dalam project adalah folder public aktif
    //    (normal lokal, VPS, atau hosting dengan document root = project/public)
    $projectPublic = $basePath . '/public';
    if (is_file($projectPublic . '/index.php')) {
        $idx = @file_get_contents($projectPublic . '/index.php');
        // Jika index.php didalamnya TIDAK naik 1 level ../ lagi (../bootstrap/app.php),
        // berarti folder public/ ini adalah document root sesungguhnya
        if ($idx === false || !preg_match('#require(_once)?\s*\(?\s*[\'"]\.\./bootstrap/app\.php[\'"]#', $idx)) {
            return $projectPublic;
        }
    }

    // 3. SHARED HOSTING DETECTION: cek public_html, www, htdocs di parent basePath
    $parentDir = dirname($basePath);
    $candidates = [
        $parentDir . '/public_html',
        $parentDir . '/www',
        $parentDir . '/htdocs',
        $parentDir . '/web',
    ];
    foreach ($candidates as $cand) {
        if (is_dir($cand) && is_file($cand . '/index.php')) {
            return $cand;
        }
    }

    return null;
};

$publicPath = $detectPublicPath();

$app = Application::configure(basePath: $basePath);

if ($publicPath !== null) {
    $app = $app->withPublicPath($publicPath);
}

return $app
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->trustProxies(
            proxies: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->trustHosts();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
