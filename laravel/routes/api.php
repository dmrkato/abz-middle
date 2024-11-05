<?php

use App\Http\Controllers\API\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/')->group(function () {
    Route::prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('', 'getList');
            Route::post('', 'createUser')
                ->middleware(\App\Http\Middleware\JwtMiddleware::class);
            Route::get('{id}', 'getUser');

        });
    });

    Route::get('positions', [\App\Http\Controllers\API\V1\PositionController::class, 'getList']);

    Route::get('token', [\App\Http\Controllers\API\V1\TokenController::class, 'getToken']);

});
