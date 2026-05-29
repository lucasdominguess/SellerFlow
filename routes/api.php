<?php

use App\Http\Controllers\Accout\CompanyController;
use App\Http\Controllers\Accout\StoreController;
use App\Http\Controllers\Accout\UserController;
use App\Http\Controllers\Accout\UserStoreController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Business\FornecedorController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\ListSuspended\ListSuspendedController;
use App\Http\Controllers\Sales\VendasController;
use App\Http\Controllers\Test\TestController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Sales\VendaItemController;
use App\Http\Controllers\Purchases\ComprasController;
use App\Http\Controllers\Purchases\CompraController;
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

    Route::middleware([JwtMiddleware::class])->group(function () {

    //Account
    Route::apiResource('user', UserController::class);
    Route::apiResource('store', StoreController::class);
    Route::apiResource('user-store', UserStoreController::class);
    Route::apiResource('company', CompanyController::class);

    //Business
    Route::apiResource('product', ProductController::class);
    Route::apiResource('fornecedor', FornecedorController::class);

    //Sales
    Route::apiResource('vendas', VendasController::class);

    Route::get('/test', [TestController::class, 'test']);
    });// end middleware jwt


    // Route::get('/test', function (Request $request) {
    //     // Log::channel('telegram')->critical('log telegram Base api test');
    //     // return Log::info('teste de log');

    // });
    Route::apiResource('/compra', CompraController::class);

}); // end v1


Route::fallback(fn() => response(["message" => 'Página não encontrada'], 404));

