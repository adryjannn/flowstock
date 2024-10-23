<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BestSellingProductsController;
use Illuminate\Support\Facades\Route;

die('To jest plik api.php');
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/best-selling-products', [BestSellingProductsController::class, 'index']);
Route::get('/test', function () {
    return 'API działa poprawnie';
});
