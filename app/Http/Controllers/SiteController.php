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

/**
 * 
 * La classe est un contrôleur pour la gestion des sites. Elle se base sur la classe
 * BaseCrudController, qui gère les opérations CRUD de base automatiquement.
 * 
 */
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
        'images.*' => 'image|mimes:jpeg,png,jpg,webp'
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

    /**
     * allowedFields définit les champs qui seront retournés dans les résultats.
     */
    protected $allowedFields = ['id', 'name', 'description', 
        'lat', 'lon', 'city', 
        'site_creation_date', 
        'site_type_id', 'created_at'
    ];

    /**
     * 
     * allowedFilters définit les champs sur lesquels on pourra effectuer de filtrage.
     * Elle s'utilise ainsi par exemple : sites?filter[city]=Yamoussoukro
     * 
     */
    protected $allowedFilters = ['name', 'city', 'site_creation_date', 'site_type_id'];

    /**
     * 
     * searchableFields définit les champs sur lesquels seront effectuer la recherche quand on fera un
     * sites?search=Abidjan par exemple
     * 
     */
    protected $searchableFields = ['name', 'description', 'city']; 

    /**
     * 
     * Cette fonction est une rédéfinition de getQuery de la classe BaseCrudController afin
     * d'ajouter des relations et des select spécifiques
     * 
     */
    public function getQuery(): Builder{

        return $this->getModel()->query()->with(['siteType','photos']);
    }

    /**
     * 
     * Cette fonction est une rédéfinition de store de la classe BaseCrudController afin
     * d'ajouter des relations et des select spécifiques
     * 
     */ 
    public function store(Request $request){
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


    public function nearby(Request $request){
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'radius' => 'nullable|integer|min:1|max:100000',
        ]);

        $sites = $this->siteService->findNearbySites(
            $request->lat,
            $request->lon,
            $request->radius ?? 20
        );

        return ApiResponse::respond(
            'Sites found successfully',
            RestServiceStatusCode::SUCCESS_OPERATION,
            200,
            $sites
        );
    }

}
