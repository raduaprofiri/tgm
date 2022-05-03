<?php

use App\Http\Controllers\Api\ItemController;
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

Route::get('items', [ItemController::class, 'index']);
Route::post('items', [ItemController::class, 'store']);
Route::put('items/{item}', [ItemController::class, 'update']);
Route::delete('items/{item}', [ItemController::class, 'destroy']);
Route::put('items/{item}/move-to/{order}', [ItemController::class, 'move']);