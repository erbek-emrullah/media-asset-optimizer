<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/', [ImageController::class, 'index']);
Route::post('/upload', [ImageController::class, 'store']);
Route::get('/images/json', [ImageController::class, 'ajaxList']);