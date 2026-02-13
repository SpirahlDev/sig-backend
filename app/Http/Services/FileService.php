<?php

namespace App\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Upload un fichier et retourne son chemin
     */
    public function upload(UploadedFile $file, string $directory = 'uploads'): array
    {
        $filename = $this->generateFilename($file);
        $path = $file->storeAs($directory, $filename, 'public');

        return [
            'url' => Storage::url($path),
            'path' => $path,
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
        ];
    }

    /**
     * Upload plusieurs fichiers
     */
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

    /**
     * Supprime un fichier
     */
    public function delete(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Supprime plusieurs fichiers
     */
    public function deleteMultiple(array $paths): int
    {
        $deleted = 0;

        foreach ($paths as $path) {
            if ($this->delete($path)) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Génère un nom de fichier unique
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);

        return "{$timestamp}_{$random}.{$extension}";
    }
}
