<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * ARTISAN COMMAND KHUSUS SHARED HOSTING TANPA SYMLINK SUPPORT.
 * Alih-alih pakai `php artisan storage:link` (yang membutuhkan symlink),
 * command ini akan MENSELKAN COPY SELURUH ISI storage/app/public/ ke public/storage/.
 *
 * Jalankan setiap kali kamu menambah file baru di storage/app/public
 * (mis: upload produk, upload logo toko baru).
 *
 * Usage:
 *   php artisan storage:copy           # copy semua (re-write jika ada perbedaan)
 *   php artisan storage:copy --force  # hapus public/storage/ dulu baru copy (clean)
 */
Artisan::command('storage:copy {--force : Hapus folder tujuan dulu (full sync)}', function () {
    $source = rtrim(storage_path('app/public'), '\\/');
    $dest   = rtrim(public_path('storage'), '\\/');

    if (! is_dir($source)) {
        $this->fail("Source tidak ditemukan: $source");
        return 1;
    }

    if ($this->option('force') && is_dir($dest)) {
        // Recursive delete dest via native
        $cleanup = function ($dir) use (&$cleanup) {
            if (! is_dir($dir)) return;
            $items = scandir($dir);
            if ($items === false) return;
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $path = $dir . DIRECTORY_SEPARATOR . $item;
                if (is_dir($path) && ! is_link($path)) {
                    $cleanup($path);
                } else {
                    @unlink($path);
                }
            }
            @rmdir($dir);
        };
        $cleanup($dest);
        $this->warn("Dihapus penuh: $dest");
    }

    if (! is_dir($dest)) {
        @mkdir($dest, 0755, true);
    }

    $copied = 0;
    $skipped = 0;
    $dirCount = 0;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        /** @var \SplFileInfo $item */
        $relative = substr($item->getPathname(), strlen($source) + 1);
        if ($relative === false || $relative === '') continue;

        $targetPath = $dest . DIRECTORY_SEPARATOR . $relative;

        if ($item->isDir()) {
            if (! is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
                $dirCount++;
                $this->line("Dir   + $relative/");
            }
        } elseif ($item->isFile()) {
            $targetDir = dirname($targetPath);
            if (! is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $needCopy = true;
            if (! $this->option('force') && is_file($targetPath)) {
                $needCopy = @filesize($item->getPathname()) !== @filesize($targetPath)
                    || @filemtime($item->getPathname()) > @filemtime($targetPath);
            }

            if ($needCopy) {
                copy($item->getPathname(), $targetPath);
                @chmod($targetPath, 0644);
                $copied++;
                $this->info("Copy  + $relative");
            } else {
                $skipped++;
            }
        }
    }

    $this->newLine();
    $this->info("Selesai! ✅");
    $this->line("Folder dibuat : $dirCount");
    $this->line("File dicopy  : $copied");
    $this->line("File di-skip : $skipped (sudah sama)");
    $this->line("Sumber       : $source");
    $this->line("Tujuan       : $dest");
    return 0;
})->purpose('Copy storage/app/public/ ke public/storage/ — alternatif storage:link untuk shared hosting tanpa symlink');

/**
 * Shortcut untuk deploy cepat: clear cache + copy storage dalam 1 command.
 */
Artisan::command('deploy:shared-hosting', function () {
    $this->info('Menjalankan optimasi + storage:copy untuk shared hosting...');
    $this->call('optimize:clear');
    $this->call('storage:copy');
    $this->info('Siap dijalankan! 🚀');
})->purpose('Optimize + storage:copy — deploy cepat untuk shared hosting tanpa Node & tanpa symlink');
