<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;

Route::get('/sites', [SiteController::class, 'index']);

Route::post('/sites', [SiteController::class, 'store']);

Route::get('/sites/{id}', [SiteController::class, 'show']);

Route::put('/sites/{id}', [SiteController::class, 'update']);

Route::delete('/sites/{id}', [SiteController::class, 'destroy']);