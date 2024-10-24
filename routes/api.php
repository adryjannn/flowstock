<?php

use App\Http\Controllers\Api\BestSellingProductsController;
use App\Http\Controllers\Api\ProducerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShopOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/best-sellers', [BestSellingProductsController::class, 'index']);
Route::middleware('auth:sanctum')->post('/orders', [ShopOrderController::class, 'store']);
Route::middleware('auth:sanctum')->get('/orders', [ShopOrderController::class, 'getOrders']);
Route::middleware('auth:sanctum')->put('/orders/update-status', [ShopOrderController::class, 'updateOrderStatus']);
Route::middleware('auth:sanctum')->get('/producers', [ProducerController::class, 'getProducers']);
Route::middleware('auth:sanctum')->post('/producers', [ProducerController::class, 'store']);
Route::middleware('auth:sanctum')->put('/producers/{id}', [ProducerController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/producers/{id}', [ProducerController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/products', [ProductController::class, 'getProducts']); // Pobieranie produktów
Route::middleware('auth:sanctum')->post('/products', [ProductController::class, 'store']); // Tworzenie produktu
Route::middleware('auth:sanctum')->put('/products/{id}', [ProductController::class, 'update']); // Aktualizacja produktu
Route::middleware('auth:sanctum')->delete('/products/{id}', [ProductController::class, 'destroy']); // Usuwanie produktu
