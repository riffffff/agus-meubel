<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
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
            return false;
        }

        $webpFilename = $filename . '.webp';
        $webpTempPath = tempnam(sys_get_temp_dir(), 'img_') . '.webp';

        try {
            $success = $this->convertWithPython($realPath, $webpTempPath);

            if (!$success) {
                $success = $this->convertWithPhpGd($realPath, $webpTempPath, $file->getMimeType());
            }

            if (!$success) {
                @unlink($webpTempPath);
                return false;
            }

            if (!file_exists($webpTempPath) || filesize($webpTempPath) < 100) {
                @unlink($webpTempPath);
                return false;
            }

            $stored = Storage::putFileAs($storageDir, new \Illuminate\Http\File($webpTempPath), $webpFilename);
            $paths['url'] = str_replace('public/', '', $stored);
            $paths['thumbnail'] = $paths['url'];

            @unlink($webpTempPath);
            return true;
        } catch (\Throwable $e) {
            @unlink($webpTempPath);
            report($e);
            return false;
        }
    }

    private function convertWithPython(string $realPath, string $webpTempPath): bool
    {
        try {
            if (!function_exists('shell_exec') || !function_exists('escapeshellarg')) {
                return false;
            }

            $pythonBin = trim((string) @shell_exec('which python3 2>/dev/null || which python 2>/dev/null'));
            if (empty($pythonBin)) {
                return false;
            }

            $processorPath = __DIR__ . '/image_processor.py';
            if (!file_exists($processorPath)) {
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
                return false;
            }

            return file_exists($webpTempPath) && filesize($webpTempPath) > 0;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    private function convertWithPhpGd(string $realPath, string $webpTempPath, string $mime): bool
    {
        try {
            if (!extension_loaded('gd') || !function_exists('imagewebp')) {
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
                    return false;
            }

            if (!$source) {
                return false;
            }

            imagepalettetotruecolor($source);
            $width = imagesx($source);
            $height = imagesy($source);
            $maxDim = 2400;
            if ($width > $maxDim || $height > $maxDim) {
                $ratio = min($maxDim / $width, $maxDim / $height);
                $newW = (int) round($width * $ratio);
                $newH = (int) round($height * $ratio);
                $dst = imagecreatetruecolor($newW, $newH);
                imagecopyresampled($dst, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
                imagedestroy($source);
                $source = $dst;
            }

            $ok = @imagewebp($source, $webpTempPath, 85);
            imagedestroy($source);
            return $ok && file_exists($webpTempPath) && filesize($webpTempPath) > 0;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
