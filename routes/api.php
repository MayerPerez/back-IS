<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/test/store', [TestController::class, 'store']);
Route::get('/test/index', [TestController::class, 'index']);
Route::get('/test/count', [TestController::class, 'countTables']);

Route::resource("/user", UserController::class)->only([
    'store', 'update', 'show', 'destroy', 'index'
]);
Route::post('/user/test/store', [UserController::class, 'storeTest']);