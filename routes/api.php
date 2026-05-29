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
    Route::get('/list', [ListSuspendedController::class, 'list']);

    //Auth
    Route::prefix('/auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refreshToken']);
    });

    //Account
    Route::apiResource('user', UserController::class);
    Route::apiResource('store', StoreController::class);
    Route::apiResource('user-store', UserStoreController::class);
    Route::apiResource('company', CompanyController::class);

    //Business
    Route::apiResource('product', ProductController::class);
    Route::apiResource('fornecedor', FornecedorController::class);

    Route::get('/test', function (Request $request) {
        Log::channel('telegram')->critical('log telegram Base api test');
        return Log::info('teste de log');
    });
}); // end v1


Route::fallback(fn() => response(["message" => 'Página não encontrada'], 404));


