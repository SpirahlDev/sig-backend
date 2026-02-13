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

    public function findNearbySites(float $lat, float $lon, float $radius)
    {
        /* On effectue directement le calcul de distance dans la base de données, au lieu de récuperer tous les
        sites et de calculer la distance dans le code.*/
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
