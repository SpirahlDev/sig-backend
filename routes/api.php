<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteTypeController;

Route::get('/sites', [SiteController::class, 'index']); 
Route::post('/sites', [SiteController::class, 'store']);
Route::get('/sites/nearby', [SiteController::class, 'nearby']);
Route::get('/sites/{id}', [SiteController::class, 'show']);
Route::delete('/sites/{id}', [SiteController::class, 'destroy']);

Route::get('/site-types', [SiteTypeController::class, 'index']);
Route::post('/site-types', [SiteTypeController::class, 'store']);
Route::get('/site-types/{id}', [SiteTypeController::class, 'show']);