<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/listings',            [ListingController::class, 'index']);
Route::get('/listings/{listing}',  [ListingController::class, 'show']);
Route::get('/categories',          [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/tags',                [TagController::class, 'index']);
Route::get('/tags/{tag}',          [TagController::class, 'show']);

// Protected (Sanctum bearer token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',                       [AuthController::class, 'me']);
    Route::post('/logout',                  [AuthController::class, 'logout']);

    Route::post('/listings',                [ListingController::class, 'store']);
    Route::put('/listings/{listing}',       [ListingController::class, 'update']);
    Route::delete('/listings/{listing}',    [ListingController::class, 'destroy']);
});
