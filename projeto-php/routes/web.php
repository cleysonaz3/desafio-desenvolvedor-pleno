<?php

use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/', ProjectStatusController::class)->name('project.status');
Route::get('/docs', [SwaggerController::class, 'index'])->name('docs.ui');
Route::get('/swagger', [SwaggerController::class, 'index'])->name('swagger.ui');
Route::get('/scalar', [SwaggerController::class, 'index'])->name('scalar.ui');
Route::get('/docs/openapi.json', [SwaggerController::class, 'json'])->name('swagger.json');
