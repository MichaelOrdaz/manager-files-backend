<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        
        Route::post('login', [App\Http\Controllers\AuthController::class, 'login'])
        ->middleware('guest')
        ->withoutMiddleware('auth:api');

        Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('account', [App\Http\Controllers\AuthController::class, 'account_data']);

        Route::prefix('users')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\UserController::class, 'index']);
            Route::post('/', [App\Http\Controllers\Api\UserController::class, 'store']);
            Route::get('/search', [App\Http\Controllers\Api\UserController::class, 'search']);
            Route::get('/{user_id}', [App\Http\Controllers\Api\UserController::class, 'show'])->whereNumber('user_id');

            Route::prefix('image')->group(function () {
                Route::post('/', [App\Http\Controllers\Api\UserAvatarController::class, 'update']);
                Route::delete('/', [App\Http\Controllers\Api\UserAvatarController::class, 'destroy']);
            });
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\RoleController::class, 'index']);
            Route::get('/{role_id}', [App\Http\Controllers\Api\RoleController::class, 'show'])->whereNumber('role_id');
        });

        Route::prefix('departments')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\DepartmentController::class, 'index']);
            Route::get('/{department_id}', [App\Http\Controllers\Api\DepartmentController::class, 'show'])->whereNumber('department_id');
        });
        
    });
});
