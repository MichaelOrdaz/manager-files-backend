<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/api/documentacion', 'App\Http\Controllers\Documentation@documentation')->name('documentation');

Route::get('documents/{document_id}/:download', [App\Http\Controllers\Api\UserDocumentController::class, 'downloadFolder'])->whereNumber(['document_id'])->name('downloadFolder');
