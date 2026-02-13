<?php

/**
 * Author : Alloué Yapi Anselme
 * Date : 03/07/2025
 * Version : 2.1.0
 * Description : Classe abstraite implémentant les fonctionnalités CRUD de base
 * pour n'importe quel modèle Eloquent.
 * À partir de cette classe, je peux définir les règles de validation, les champs autorisés pour l'affichage,
 * les champs autorisés pour le filtrage etc, d'une resource
 */

namespace App\Http\Controllers;

use App\Helpers\QueryParamsHandler;
use App\Helpers\ApiResponse;
use App\Interfaces\CrudControllerInterface;
use App\Utils\RestServiceStatusCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;


/**
 * Classe abstraite implémentant les fonctionnalités CRUD de base
 * pour n'importe quel modèle Eloquent
 */
abstract class BaseCrudController implements CrudControllerInterface
{
    /**
     * Le modèle Eloquent associé à ce contrôleur
     */
    protected $model;

    /**
     * Les règles de validation pour la création
     */
    protected $storeRules = [];

    /**
     * Les règles de validation pour la mise à jour
     */
    protected $updateRules = [];

    /**
     * Les champs autorisés pour l'affichage
     */
    protected $allowedFields = ['*'];

    /**
     * Les champs autorisés pour le filtrage
     */
    protected $allowedFilters = [];

    /**
     * Les champs utilisés pour la recherche
     */
    protected $searchableFields = ['name', 'title', 'description'];

    /**
     * Retourne le modèle associé au contrôleur
     */
    protected function getModel(): Model
    {
        if (!$this->model) {
            throw new \Exception("Model property must be set in the concrete controller class");
        }

        return new $this->model();
    }

    public function getQuery(): Builder
    {
        return $this->getModel()->query();
    }

    /**
     * Retourne la liste paginée des ressources
     */
    public function index(Request $request)
    {
        try {
            // $model = $this->getModel();
            $query = $this->getQuery();
            // Utilisation du QueryParamsHandler pour le filtrage et la pagination
            $handler = new QueryParamsHandler(
                $query,
                $request,
                $this->allowedFields,
                $this->allowedFilters,
                $this->searchableFields
            );

            $results = $handler->handle()->paginate();

            return ApiResponse::autoRespond(
                RestServiceStatusCode::SUCCESS_OPERATION,
                200,
                $results
            );
        } catch (\Exception $e) {

            return ApiResponse::respond($e->getMessage(),
                RestServiceStatusCode::FAILED_OPERATION,
                500,
            );

        }
    }

    /**
     * Retourne une ressource spécifique
     */
    public function show($id)
    {
        try {
            $model = $this->getQuery();
            $resource = $model->findOrFail($id);

            return ApiResponse::autoRespond(
                RestServiceStatusCode::SUCCESS_OPERATION,
                200,
                $resource
            );
        } catch (\Exception $e) {
            return ApiResponse::respond('Not Found',RestServiceStatusCode::ERROR_RESSOURCE_NOT_FOUND,404);
        }
    }

    /**
     * Stocke une nouvelle ressource
     */
    public function store(Request $request)
    {
        try {
            // Valider la requête
            $validator = Validator::make($request->all(), $this->storeRules);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            DB::beginTransaction();

            $model = $this->getModel();
            $resource = $model->create($request->all());

            DB::commit();

            return response()->json([
                'status_code' => 201,
                'status_message' => 'Created',
                'data' => $resource
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => RestServiceStatusCode::ERROR_DATA_INVALID,
                'status_message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'status_message' => 'Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour une ressource spécifique
     */
    public function update(Request $request, $id)
    {
        try {
            $model = $this->getModel();
            $resource = $model->findOrFail($id);

            // Valider la requête
            $validator = Validator::make($request->all(), $this->updateRules);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            DB::beginTransaction();

            $resource->update($request->all());

            DB::commit();

            return ApiResponse::respond(
                'Updated',
                RestServiceStatusCode::SUCCESS_OPERATION,
                200,
                $resource
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            return ApiResponse::respond(
                'Validation Error',
                RestServiceStatusCode::ERROR_DATA_INVALID,
                422,
                $e->errors()
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::respond(
                "Unexpected error",
                RestServiceStatusCode::FAILED_OPERATION,
                500
            );
        }
    }

    /**
     * Supprime une ressource spécifique
     */
    public function destroy($id)
    {
        try {
            $model = $this->getModel();
            $resource = $model->findOrFail($id);

            DB::beginTransaction();

            $resource->delete();

            DB::commit();

            return ApiResponse::respond(
                'Deleted',
                RestServiceStatusCode::SUCCESS_OPERATION,
                200,
                null
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::respond(
                "Unexpected error",
                RestServiceStatusCode::FAILED_OPERATION,
                500
            );
        }
    }

    /**
     * Suppression douce d'une ressource
     */
    public function softDelete($id)
    {
        try {
            $model = $this->getModel();

            // Vérifier si le modèle utilise le trait SoftDeletes
            if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                throw new \Exception("Model does not use SoftDeletes trait");
            }

            $resource = $model->findOrFail($id);

            DB::beginTransaction();

            // Vérifier si le modèle a la colonne is_active
            if (in_array('is_active', $model->getFillable()) || array_key_exists('is_active', $model->getAttributes())) {
                $resource->update([
                    'is_active' => false,
                ]);
            }

            // Mettre à jour les champs personnalisés avant la suppression douce
            if (method_exists($model, 'getDeletedAtColumn')) {
                $resource->update([
                    'deleted_at' => now(),
                    $model->getDeletedAtColumn() => now()
                ]);
            }

            $resource->delete();

            DB::commit();

            return ApiResponse::success();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::respond(
                "Unexpected error".$e->getMessage(),
                RestServiceStatusCode::FAILED_OPERATION,
                500
            );
        }
    }

    /**
     * Basculer le statut (actif/inactif) d'une ressource
     */
    public function toggleStatus($id, $status)
    {
        try {
            $model = $this->getModel();
            $resource = $model->findOrFail($id);

            // Convertir la chaîne en booléen
            $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

            DB::beginTransaction();

            $resource->update([
                'is_active' => $status,
                'updated_at' => now()
            ]);

            DB::commit();

            return ApiResponse::respond(
                'Status Updated',
                RestServiceStatusCode::SUCCESS_OPERATION,
                200,
                $resource
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::respond(
                "Unexpected error",
                RestServiceStatusCode::FAILED_OPERATION,
                500
            );
        }
    }

    /**
     * Retourne des statistiques sur le modèle
     */
    public function stats()
    {
        try {
            $model = $this->getModel();
            $now = now();

            $stats = [
                'total' => $model->count(),
                'created_today' => $model->whereDate('created_at', $now->format('Y-m-d'))->count(),
                'created_this_week' => $model->whereBetween('created_at', [
                    $now->startOfWeek()->format('Y-m-d'),
                    $now->endOfWeek()->format('Y-m-d')
                ])->count(),
                'created_this_month' => $model->whereMonth('created_at', $now->month)
                                            ->whereYear('created_at', $now->year)
                                            ->count(),
                'latest_entry' => $model->latest('created_at')->first(['id', 'created_at']),
                'oldest_entry' => $model->oldest('created_at')->first(['id', 'created_at'])
            ];

            // Vérifier si le modèle a la colonne 'active'
            if (in_array('active', $model->getFillable()) || in_array('active', $model->getAttributes())) {
                $stats['active'] = $model->where('active', true)->count();
                $stats['inactive'] = $model->where('active', false)->count();
            }

            // Vérifier si le modèle a la colonne 'updated_at'
            if ($model->timestamps || in_array('updated_at', $model->getFillable())) {
                $stats['updated_today'] = $model->whereDate('updated_at', $now->format('Y-m-d'))->count();
                $stats['updated_this_week'] = $model->whereBetween('updated_at', [
                    $now->startOfWeek()->format('Y-m-d'),
                    $now->endOfWeek()->format('Y-m-d')
                ])->count();
                $stats['updated_this_month'] = $model->whereMonth('updated_at', $now->month)
                                                    ->whereYear('updated_at', $now->year)
                                                    ->count();
            }

            // Si le modèle utilise SoftDeletes
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                $stats['deleted'] = $model->onlyTrashed()->count();
                $stats['deleted_this_month'] = $model->onlyTrashed()
                    ->whereMonth('deleted_at', $now->month)
                    ->whereYear('deleted_at', $now->year)
                    ->count();
            }

            return ApiResponse::respond(
                'Success',
                RestServiceStatusCode::SUCCESS_OPERATION,
                200,
                $stats
            );
        } catch (\Exception $e) {
            return ApiResponse::respond(
                "Unexpected error",
                RestServiceStatusCode::FAILED_OPERATION,
                500
            );
        }
    }

    /**
     * Retourne la liste des éléments dans la corbeille (supprimés avec SoftDelete)
     */
    public function trash(Request $request)
    {
        try {
            $model = $this->getModel();

            // Vérifier si le modèle utilise le trait SoftDeletes
            if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                throw new \Exception("Model does not use SoftDeletes trait");
            }

            $query = $model->onlyTrashed();

            // Utilisation du QueryParamsHandler pour le filtrage et la pagination
            $handler = new QueryParamsHandler(
                $query,
                $request,
                $this->allowedFields,
                $this->allowedFilters,
                $this->searchableFields
            );

            $results = $handler->handle()->paginate();

            return ApiResponse::respond(
                'Success',
                RestServiceStatusCode::SUCCESS_OPERATION,
                200,
                $results
            );
        } catch (\Exception $e) {
            return ApiResponse::respond(
                "Unexpected error",
                RestServiceStatusCode::FAILED_OPERATION,
                500
            );
        }
    }
}
