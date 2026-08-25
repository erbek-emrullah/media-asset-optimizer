<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', [ImageController::class, 'upload']);
Route::post('/upload', [ImageController::class, 'store']);
Route::get('/list', [ImageController::class, 'list']);