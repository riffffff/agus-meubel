<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ImageService
{
    public function handleUpload(UploadedFile $file, string $dir = 'products'): array
    {
        $filename = Str::random(24) . '_' . time();
        $storageDir = "public/{$dir}";

        $paths = [
            'url'       => null,
            'thumbnail' => null,
        ];

        if ($this->convertToWebp($file, $storageDir, $filename, $paths)) {
            return $paths;
        }

        // Fallback: simpan format asli jika konversi WebP gagal
        Log::warning('ImageService: konversi WebP gagal, menyimpan format asli.', [
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $file->getMimeType(),
            'size'          => $file->getSize(),
            'dir'           => $storageDir,
        ]);

        $fallbackName = $filename . '.' . $file->getClientOriginalExtension();
        $stored = $file->storeAs($storageDir, $fallbackName);
        $paths['url'] = str_replace('public/', '', $stored);
        $paths['thumbnail'] = $paths['url'];

        return $paths;
    }

    public function deleteIfExists(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = 'public/' . ltrim($path, '/');
        if (Storage::exists($fullPath)) {
            Storage::delete($fullPath);
        }
    }

    private function convertToWebp(UploadedFile $file, string $storageDir, string $filename, array &$paths): bool
    {
        $realPath = $file->getRealPath();
        if (!$realPath) {
            Log::warning('ImageService: getRealPath() mengembalikan null.', [
                'original_name' => $file->getClientOriginalName(),
            ]);
            return false;
        }

        $webpFilename = $filename . '.webp';
        $webpTempPath = tempnam(sys_get_temp_dir(), 'img_') . '.webp';

        try {
            $success = $this->convertWithPython($realPath, $webpTempPath);

            if (!$success) {
                Log::debug('ImageService: konversi Python gagal, mencoba GD.', [
                    'file' => $file->getClientOriginalName(),
                ]);
                $success = $this->convertWithPhpGd($realPath, $webpTempPath, $file->getMimeType());
            }

            if (!$success) {
                Log::warning('ImageService: semua metode konversi WebP gagal.', [
                    'file' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                ]);
                @unlink($webpTempPath);
                return false;
            }

            if (!file_exists($webpTempPath) || filesize($webpTempPath) < 100) {
                Log::warning('ImageService: file WebP hasil konversi kosong atau terlalu kecil.', [
                    'temp_path' => $webpTempPath,
                    'size'      => file_exists($webpTempPath) ? filesize($webpTempPath) : 0,
                ]);
                @unlink($webpTempPath);
                return false;
            }

            $stored = Storage::putFileAs($storageDir, new \Illuminate\Http\File($webpTempPath), $webpFilename);
            $paths['url'] = str_replace('public/', '', $stored);
            $paths['thumbnail'] = $paths['url'];

            @unlink($webpTempPath);

            Log::debug('ImageService: konversi WebP berhasil.', [
                'stored_path' => $paths['url'],
                'original'    => $file->getClientOriginalName(),
            ]);

            return true;
        } catch (\Throwable $e) {
            @unlink($webpTempPath);
            Log::error('ImageService: exception saat konversi WebP.', [
                'message' => $e->getMessage(),
                'file'    => $file->getClientOriginalName(),
                'trace'   => $e->getTraceAsString(),
            ]);
            report($e);
            return false;
        }
    }

    private function convertWithPython(string $realPath, string $webpTempPath): bool
    {
        try {
            if (!function_exists('shell_exec') || !function_exists('escapeshellarg')) {
                Log::debug('ImageService: shell_exec atau escapeshellarg tidak tersedia, skip Python converter.');
                return false;
            }

            $pythonBin = trim((string) @shell_exec('which python3 2>/dev/null || which python 2>/dev/null'));
            if (empty($pythonBin)) {
                Log::debug('ImageService: Python tidak ditemukan di server, skip Python converter.');
                return false;
            }

            $processorPath = __DIR__ . '/image_processor.py';
            if (!file_exists($processorPath)) {
                Log::warning('ImageService: image_processor.py tidak ditemukan.', [
                    'expected_path' => $processorPath,
                ]);
                return false;
            }

            $process = new Process([
                $pythonBin,
                $processorPath,
                $realPath,
                $webpTempPath,
            ]);
            $process->setTimeout(60);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::warning('ImageService: Python converter gagal.', [
                    'exit_code' => $process->getExitCode(),
                    'stderr'    => $process->getErrorOutput(),
                ]);
                return false;
            }

            return file_exists($webpTempPath) && filesize($webpTempPath) > 0;
        } catch (\Throwable $e) {
            Log::error('ImageService: exception di convertWithPython.', [
                'message' => $e->getMessage(),
            ]);
            report($e);
            return false;
        }
    }

    private function convertWithPhpGd(string $realPath, string $webpTempPath, string $mime): bool
    {
        try {
            if (!extension_loaded('gd') || !function_exists('imagewebp')) {
                Log::warning('ImageService: ekstensi GD tidak tersedia atau imagewebp tidak didukung.');
                return false;
            }

            $source = null;
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $source = @imagecreatefromjpeg($realPath);
                    break;
                case 'image/png':
                    $source = @imagecreatefrompng($realPath);
                    break;
                case 'image/gif':
                    $source = @imagecreatefromgif($realPath);
                    break;
                case 'image/webp':
                    return @copy($realPath, $webpTempPath);
                default:
                    Log::warning('ImageService: tipe MIME tidak didukung untuk konversi GD.', [
                        'mime' => $mime,
                    ]);
                    return false;
            }

            if (!$source) {
                Log::warning('ImageService: GD gagal membaca file gambar.', [
                    'path' => $realPath,
                    'mime' => $mime,
                ]);
                return false;
            }

            imagepalettetotruecolor($source);
            $width  = imagesx($source);
            $height = imagesy($source);
            $maxDim = 2400;

            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newW  = (int) round($width * $ratio);
                $newH  = (int) round($height * $ratio);
                $dst   = imagecreatetruecolor($newW, $newH);
                imagecopyresampled($dst, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
                imagedestroy($source);
                $source = $dst;

                Log::debug('ImageService: gambar di-resize sebelum konversi WebP.', [
                    'original' => "{$width}x{$height}",
                    'resized'  => "{$newW}x{$newH}",
                ]);
            }

            $ok = @imagewebp($source, $webpTempPath, 85);
            imagedestroy($source);

            if (!$ok) {
                Log::warning('ImageService: imagewebp() mengembalikan false.', [
                    'dest' => $webpTempPath,
                ]);
            }

            return $ok && file_exists($webpTempPath) && filesize($webpTempPath) > 0;
        } catch (\Throwable $e) {
            Log::error('ImageService: exception di convertWithPhpGd.', [
                'message' => $e->getMessage(),
                'mime'    => $mime,
            ]);
            report($e);
            return false;
        }
    }
}
