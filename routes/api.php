<?php

use App\Http\Controllers\Accout\CompanyController;
use App\Http\Controllers\Accout\StoreController;
use App\Http\Controllers\Accout\UserController;
use App\Http\Controllers\Accout\UserStoreController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Business\FornecedorController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\ListSuspended\ListSuspendedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
Route::prefix('/v1')->group(function () {

    //List Suspended
    Route::prefix('/list')->group(function () {
        route::get('/', [ListSuspendedController::class, 'list']);
    });

    //Auth
    Route::prefix('/auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refreshToken']);
    });
    //Account
    Route::prefix('/user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'delete']);
    });
    Route::prefix('/store')->group(function () {
        Route::get('/', [StoreController::class, 'index']);
        Route::get('/{store}', [StoreController::class, 'show']);
        Route::post('/', [StoreController::class, 'store']);
        Route::put('/{store}', [StoreController::class, 'update']);
        Route::delete('/{store}', [StoreController::class, 'delete']);
    });
    Route::prefix('/user-store')->group(function () {
        Route::get('/', [UserStoreController::class, 'index']);
        Route::get('/{userStore}', [UserStoreController::class, 'show']);
        Route::post('/', [UserStoreController::class, 'store']);
        Route::put('/{userStore}', [UserStoreController::class, 'update']);
        Route::delete('/{userStore}', [UserStoreController::class, 'delete']);
    });
    Route::prefix('/company')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::get('/{company}', [CompanyController::class, 'show']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::put('/{company}', [CompanyController::class, 'update']);
        Route::delete('/{company}', [CompanyController::class, 'delete']);
    });

    //Business
    Route::prefix('/product')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{product}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'delete']);
    });
    Route::prefix('/fornecedor')->group(function () {
        Route::get('/', [FornecedorController::class, 'index']);
        Route::get('/{fornecedor}', [FornecedorController::class, 'show']);
        Route::post('/', [FornecedorController::class, 'store']);
        Route::put('/{fornecedor}', [FornecedorController::class, 'update']);
        Route::delete('/{fornecedor}', [FornecedorController::class, 'delete']);
    });



    Route::get('/test', function (Request $request) {

        Log::channel('telegram')->critical('log telegram Base api test');
        return Log::info('teste de log');
    });
}); // end v1


Route::fallback(fn() => response(["message" => 'Página não encontrada'], 404));


