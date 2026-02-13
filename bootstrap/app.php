<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\ApiResponse;
use App\Utils\RestServiceStatusCode;
use App\Exceptions\BusinessLogicException;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return ApiResponse::respond(
                    'Erreur de validation',
                    RestServiceStatusCode::ERROR_DATA_INVALID,
                    422,
                    $e->errors()
                );
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return ApiResponse::respond(
                    'Non authentifié',
                    RestServiceStatusCode::ERROR_ACCESS_DENIED,
                    401
                );
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return ApiResponse::respond(
                    'Ressource non trouvée',
                    RestServiceStatusCode::ERROR_RESSOURCE_NOT_FOUND,
                    404
                );
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return ApiResponse::respond(
                    'Route non trouvée',
                    RestServiceStatusCode::ERROR_RESSOURCE_NOT_FOUND,
                    404
                );
            }
        });

        $exceptions->render(function (\PDOException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return ApiResponse::respond(
                    'Erreur de base de données',
                    RestServiceStatusCode::SERVER_ERROR,
                    500
                );
            }
        });   
        
        $exceptions->render(function (BusinessLogicException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return ApiResponse::respond(
                    $e->getMessage(),
                    RestServiceStatusCode::SERVER_ERROR,
                    HTTPResponse::HTTP_BAD_REQUEST
                );
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                if (config('app.debug')) {
                    return ApiResponse::respond(
                        $e->getMessage(),
                        RestServiceStatusCode::SERVER_ERROR,
                        500,
                        [
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTrace()
                        ]
                    );
                }

                return ApiResponse::respond($e->getMessage() ?? 'Une erreur inattendue s\'est produite',
                    RestServiceStatusCode::SERVER_ERROR,
                    500
                );
            }
        });
    })->create();
