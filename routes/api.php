<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NegocioController;

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
//Rutas para el controlador de pruebas
Route::post('/test/store', [TestController::class, 'store']);
Route::get('/test/index', [TestController::class, 'index']);
Route::get('/test/count', [TestController::class, 'countTables']);

//Rutas para el controlador de usuarios
Route::resource("/user", UserController::class)->only([
    'store', 'update', 'show', 'destroy', 'index'
]);
Route::post('/user/test/store', [UserController::class, 'storeTest']);

//Rutas para el controlador de negocios
Route::resource("/negocio", NegocioController::class)->only([
    'store', 'update', 'show', 'destroy', 'index'
]);
Route::get('/negocio/test/store', [NegocioController::class, 'storeTest']);
Route::get('/negocio/create/table', [NegocioController::class, 'createTable']);
Route::get('/negocio/delete/table', [NegocioController::class, 'dropTable']);

//Rutas para el controlador de productos
Route::get('/producto/test/store', [ProductoController::class, 'storeTest']);
Route::get('/producto/create/table', [ProductoController::class, 'createTable']);
Route::get('/producto/delete/table', [ProductoController::class, 'dropTable']);
Route::resource("/producto", ProductoController::class)->only([
    'store', 'update', 'index', 'show', 'destroy'
]);
