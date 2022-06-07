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

Route::get('v1/verify-auth', [App\Http\Controllers\AuthController::class, 'verifyAuth']);


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
            Route::post('/{user_id}', [App\Http\Controllers\Api\UserController::class, 'update'])->whereNumber('user_id');
            Route::delete('/{user_id}', [App\Http\Controllers\Api\UserController::class, 'destroy'])->whereNumber('user_id');

            Route::prefix('image')->group(function () {
                Route::post('/', [App\Http\Controllers\Api\UserAvatarController::class, 'update']);
                Route::delete('/', [App\Http\Controllers\Api\UserAvatarController::class, 'destroy']);
            });

            Route::post('/{user_id}/change-password', [App\Http\Controllers\Api\UserPasswordController::class, 'update'])->whereNumber('user_id');

            Route::post('/{user_id}/permissions', [App\Http\Controllers\Api\UserPermissionController::class, 'store'])->whereNumber('user_id');
            Route::post('/*/permissions', [App\Http\Controllers\Api\UserPermissionController::class, 'storeMany']);
        });

        Route::prefix('documents')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\UserDocumentController::class, 'index']);
            Route::get('/{document_id}', [App\Http\Controllers\Api\UserDocumentController::class, 'show'])->whereNumber(['document_id']);
            Route::post('/', [App\Http\Controllers\Api\UserDocumentController::class, 'storeFile']);
            Route::post('/{document_id}', [App\Http\Controllers\Api\UserDocumentController::class, 'update'])->whereNumber(['document_id']);
            Route::delete('/{document_id}', [App\Http\Controllers\Api\UserDocumentController::class, 'destroy'])->whereNumber(['document_id']);
            Route::post('/{document_id}/:rename', [App\Http\Controllers\Api\UserDocumentController::class, 'rename'])->whereNumber(['document_id']);

            Route::prefix('{document_id}/tags')->group(function () {
                Route::post('/', [App\Http\Controllers\Api\UserDocumentTagsController::class, 'store'])->whereNumber(['document_id']);
                Route::delete('/{tag_name}', [App\Http\Controllers\Api\UserDocumentTagsController::class, 'destroy'])->whereNumber(['document_id']);
            });
        });

        Route::prefix('folders')->group(function () {
            Route::post('/', [App\Http\Controllers\Api\UserDocumentController::class, 'storeFolder']);
        });

        Route::prefix('admin')->group(function () {
            Route::post('users/{user_id}/reset-password', [App\Http\Controllers\Api\UserResetPasswordController::class, 'update'])->whereNumber('user_id');
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\RoleController::class, 'index']);
            Route::get('/{role_id}', [App\Http\Controllers\Api\RoleController::class, 'show'])->whereNumber('role_id');
        });

        Route::prefix('departments')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\DepartmentController::class, 'index']);
            Route::get('/{department_id}', [App\Http\Controllers\Api\DepartmentController::class, 'show'])->whereNumber('department_id');
        });

        Route::prefix('analyst-permissions')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\AnalystPermissionController::class, 'index']);
        });

        Route::prefix('share-permissions')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\SharePermissionController::class, 'index']);
        });

        Route::prefix('share-documents')->group(function () {
            Route::prefix('/for-me')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\ShareDocumentForMeController::class, 'index']);
                Route::get('/{document_id}', [App\Http\Controllers\Api\ShareDocumentForMeController::class, 'show'])->whereNumber('document_id');
            });

            Route::prefix('/by-me')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\ShareDocumentController::class, 'index']);
            });

            Route::prefix('{document_id}/users')->group(function () {
                Route::get('/', [App\Http\Controllers\Api\ShareDocumentUserController::class, 'index'])->whereNumber(['document_id']);
                Route::get('/{user_id}', [App\Http\Controllers\Api\ShareDocumentUserController::class, 'show'])->whereNumber(['user_id', 'document_id']);
                Route::post('/', [App\Http\Controllers\Api\ShareDocumentUserController::class, 'store'])->whereNumber(['document_id']);
                Route::delete('/{user_id}', [App\Http\Controllers\Api\ShareDocumentUserController::class, 'delete'])->whereNumber(['user_id', 'document_id']);
            });
        });
    });
});