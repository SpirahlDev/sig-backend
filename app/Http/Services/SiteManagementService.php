<?php

namespace App\Http\Services;

use App\Models\Site;
use App\Models\Photo;
use App\Exceptions\BusinessLogicException;
use Illuminate\Support\Facades\DB;
use App\Http\Services\FileService;

class SiteManagementService
{
    public function __construct(private FileService $fileService) {} 

    public function createSite(array $data, array $images = []): Site
    {
        if (!$this->validateCoordinates($data['lat'], $data['lon'])) {
            throw new BusinessLogicException('Invalid coordinates');
        }

        // Petite transaction pour s'assurer les données du patrimoine et ses photos sont bien enregistrées
        return DB::transaction(function () use ($data, $images) {
            $site = Site::create($data);

            // Upload et associer les images
            if (!empty($images)) {
                $this->attachPhotos($site, $images);
            }

            return $site;
        });
    }

    public function updateSite(int $id, array $data, array $images = []): Site
    {
        $site = Site::findOrFail($id);

        return DB::transaction(function () use ($site, $data, $images) {
            $site->update($data);

            // Ajouter de nouvelles images si fournies
            if (!empty($images)) {
                $this->attachPhotos($site, $images);
            }

            return $site;
        });
    }

   
    public function attachPhotos(Site $site, array $images): void
    {
        $uploadedFiles = $this->fileService->uploadMultiple($images, 'sites');

        foreach ($uploadedFiles as $file) {
            $photo = Photo::create([
                'url' => $file['url'],
                'size' => $file['size'],
            ]);

            $site->photos()->attach($photo->id);
        }
    }

    public function detachPhoto(Site $site, int $photoId): bool
    {
        $photo = $site->photos()->find($photoId);

        if (!$photo) {
            return false;
        }

        // Extraire le path depuis l'URL
        $path = str_replace('/storage/', '', $photo->url);

        // Supprimer le fichier
        $this->fileService->delete($path);

        // Détacher et supprimer la photo
        $site->photos()->detach($photoId);
        $photo->delete();

        return true;
    }

    /**
     * Remplace toutes les photos d'un site
     */
    public function replacePhotos(Site $site, array $images): void
    {
        // Supprimer les anciennes photos
        foreach ($site->photos as $photo) {
            $this->detachPhoto($site, $photo->id);
        }

        // Ajouter les nouvelles
        if (!empty($images)) {
            $this->attachPhotos($site, $images);
        }
    }

    public function getNearSites(float $lat, float $lon, float $radius)
    {
        // Formule Haversine pour calculer la distance
        return Site::selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?)) +
                sin(radians(?)) * sin(radians(lat))
            )) AS distance
        ", [$lat, $lon, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->with('siteType', 'photos')
            ->get();
    }

    public function validateCoordinates(float $lat, float $lon): bool
    {
        if ($lat < -90.0 || $lat > 90.0) {
            return false;
        }

        if ($lon < -180.0 || $lon > 180.0) {
            return false;
        }

        return true;
    }
}
