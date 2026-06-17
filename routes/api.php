<?php

use App\Http\Controllers\Accout\CompanyController;
use App\Http\Controllers\Accout\StoreController;
use App\Http\Controllers\Accout\UserController;
use App\Http\Controllers\Accout\UserStoreController;
use App\Http\Controllers\Adjustment\StockAdjustmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Business\FornecedorController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Business\ValidateProductController;
use App\Http\Controllers\Finance\AccountPayableController;
use App\Http\Controllers\Finance\AccountReceivableController;
use App\Http\Controllers\Finance\CashFlowController;
use App\Http\Controllers\ListSuspended\ListSuspendedController;
use App\Http\Controllers\Purchases\CompraController;
use App\Http\Controllers\Sales\VendasController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Test\TestController;
use App\Http\Middleware\JwtMiddleware;
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

    Route::middleware([JwtMiddleware::class])->group(function () {

    //Account
    Route::apiResource('user', UserController::class);
    Route::apiResource('store', StoreController::class);
    Route::apiResource('user-store', UserStoreController::class);
    Route::apiResource('company', CompanyController::class);

    //Business
    Route::apiResource('product', ProductController::class);
    // Route::apiResource('validate-product', ValidateProductController::class);
    Route::post('/validate-product',[ValidateProductController::class, 'validate']);
    Route::post('/validate-product-save',[ValidateProductController::class, 'store']);

    Route::apiResource('fornecedor', FornecedorController::class);

    //Purchases
    Route::apiResource('/compra', CompraController::class);
    //Sales
    Route::apiResource('/vendas', VendasController::class);

    //Stock
    // Ajuste de estoque é imutável: sem update/destroy via API (ver StockAdjustmentService)
    Route::apiResource('/stock-adjustment', StockAdjustmentController::class)->only(['index', 'show', 'store']);
    // precisa vir antes do apiResource('/stock', ...) para não ser capturada por GET /stock/{stock}
    Route::get('/stock-check-quantity', [StockController::class, 'checkQuantityProductsInStock']);
    Route::apiResource('/stock', StockController::class);

    //Finance
    Route::get('/finance/cash-flow', [CashFlowController::class, 'index']);
    Route::apiResource('/account-payable', AccountPayableController::class);
    Route::apiResource('/account-receivable', AccountReceivableController::class);

    Route::get('/test', [TestController::class, 'test']);
    });// end middleware jwt


    // Route::get('/test', function (Request $request) {
        //     // Log::channel('telegram')->critical('log telegram Base api test');
        //     // return Log::info('teste de log');
        // });


}); // end v1


Route::fallback(fn() => response(["message" => 'Página não encontrada'], 404));


