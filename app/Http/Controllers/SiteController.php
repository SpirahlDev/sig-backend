<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\BaseCrudController;
use App\Http\Services\SiteManagementService;
use App\Models\Site;
use App\Models\SiteType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Utils\RestServiceStatusCode;

class SiteController extends BaseCrudController
{

    public function __construct(private SiteManagementService $siteService){}

    protected $model = Site::class;

    protected $storeRules = [
        'name' => 'required|string|max:45',
        'description' => 'nullable|string',
        'lat' => 'required|string|max:200',
        'lon' => 'required|string|max:200',
        'city' => 'nullable|string|max:200',
        'site_creation_date' => 'nullable|date',
        'site_type_id' => 'nullable|exists:site_type,id',
        'images' => 'required|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
    ];

    protected $updateRules = [
        'name' => 'sometimes|string|max:45',
        'description' => 'nullable|string',
        'lat' => 'sometimes|string|max:200',
        'lon' => 'sometimes|string|max:200',
        'city' => 'nullable|string|max:200',
        'site_creation_date' => 'nullable|date',
        'site_type_id' => 'nullable|exists:site_type,id',
    ];

    protected $allowedFields = ['id', 'name', 'description', 'lat', 'lon', 'city', 'site_creation_date', 'site_type_id', 'created_at'];

    protected $allowedFilters = ['name', 'city', 'site_creation_date', 'site_type_id'];

    protected $searchableFields = ['name', 'description', 'city'];

    public function getQuery(): Builder
    {
        return $this->getModel()->query()->with(['siteType', 'photos']);
    }

    public function store(Request $request)
    {
        $request->validate($this->storeRules);

        if ($request->site_type_id) {
            $siteType = SiteType::find($request->site_type_id);
            if (!$siteType) {
                return ApiResponse::respond(
                    'Site type not found',
                    RestServiceStatusCode::ERROR_RESSOURCE_NOT_FOUND,
                    404
                );
            }
        }

        $images = $request->file('images', []);
        $site = $this->siteService->createSite($request->except('images'), $images);
        $site->load(['siteType', 'photos']);

        return ApiResponse::respond(
            'Site created successfully',
            RestServiceStatusCode::SUCCESS_OPERATION,
            201,
            $site
        );
    }

    public function deletePhoto(Site $site, int $photoId)
    {
        $deleted = $this->siteService->detachPhoto($site, $photoId);

        if (!$deleted) {
            return ApiResponse::respond(
                'Photo not found',
                RestServiceStatusCode::ERROR_RESSOURCE_NOT_FOUND,
                404
            );
        }

        return ApiResponse::respond(
            'Photo deleted successfully',
            RestServiceStatusCode::SUCCESS_OPERATION,
            200
        );
    }
}
