<?php

use App\Http\Controllers\Accout\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


Route::prefix('/v1')->group(function () {


        Route::prefix('/user')->group( function() {
            Route::get('/',[UserController::class,'index']);
            Route::get('/{id}',[UserController::class,'show']);
            Route::post('/',[UserController::class,'store']);
            Route::put('/{user}',[UserController::class,'update']);
            Route::delete('/{user}',[UserController::class,'delete']);
        });



















    Route::get('/test', function (Request $request) {

        Log::channel('telegram')->critical('log telegram Base api test');
        return Log::info('teste de log');
    });
}); // end v1


Route::fallback(fn() => response(["message" => 'Página não encontrada'], 404));

