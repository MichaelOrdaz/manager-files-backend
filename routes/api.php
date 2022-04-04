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

    Route::prefix('auth')->group(function () {
        Route::post('login', [App\Http\Controllers\AuthController::class,'login']);
        Route::get('getRImage/{name}', ['App\Http\Controllers\ResourceController', 'showResource']);

        Route::middleware(['auth:api'])->group(function() {

            Route::get('logout', [App\Http\Controllers\AuthController::class,'logout']);
            Route::get('user', [App\Http\Controllers\AuthController::class,'user']);
            Route::get('account_data', [App\Http\Controllers\AuthController::class,'account_data']);

            Route::post('signup', [App\Http\Controllers\AuthController::class,'signup']);

            Route::get('permissions/{id}', ['App\Http\Controllers\UserController', 'permissions']);
            Route::get('views/{id}', ['App\Http\Controllers\UserController', 'views']);
            Route::get('roles', ['App\Http\Controllers\Api\RolesController', 'list']);
            Route::get('roles/{id}', ['App\Http\Controllers\UserController', 'roles']);
        });
    });
});
