<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ImageService
{
    /**
     * Process an uploaded image: convert to WebP, resize to max 1400px width,
     * and store it in the public disk.
     *
     * @param UploadedFile $file
     * @param string $subFolder
     * @param int $maxWidth
     * @param int $quality
     * @return string Path to the stored file relative to the storage disk root
     */
    public function process(UploadedFile $file, string $subFolder = 'images', int $maxWidth = 1400, int $quality = 75): string
    {
        // 1. Save uploaded file to a temporary location
        $tempPath = $file->getRealPath();
        
        // 2. Generate unique name for WebP output
        $fileName = Str::uuid() . '.webp';
        
        // 3. Define the destination path inside local public storage
        // Ensure directory exists
        Storage::disk('public')->makeDirectory($subFolder);
        
        // Get absolute path for output in the public storage
        $outputPath = Storage::disk('public')->path($subFolder . '/' . $fileName);
        
        // 4. Run python processor script
        $pythonScript = app_path('Services/image_processor.py');
        
        $process = new Process([
            'python3',
            $pythonScript,
            $tempPath,
            $outputPath,
            (string) $maxWidth,
            (string) $quality
        ]);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        // Return public path: 'images/filename.webp' which can be resolved to url using Storage::url()
        return $subFolder . '/' . $fileName;
    }
}
