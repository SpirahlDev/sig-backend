<?php

namespace App\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Utils\Utils;

class FileService
{
   
    public function upload(UploadedFile $file, string $directory = 'uploads'): array
    {
        $filename = $this->generateFilename($file);
        $path = $file->storeAs($directory, $filename, 'public');

        return [
            'url' => Storage::url($path),
            'path' => $path,
            'size' => Utils::getSize($file,'MB'),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
        ];
    }

   
    public function uploadMultiple(array $files, string $directory = 'uploads'): array
    {
        $uploaded = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploaded[] = $this->upload($file, $directory);
            }
        }

        return $uploaded;
    }

   
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);

        return "{$timestamp}_{$random}.{$extension}";
    }
}
