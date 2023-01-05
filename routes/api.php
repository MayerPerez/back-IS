<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PedidoController;

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
//Login
Route::post('/login/cliente', [LoginController::class, 'loginCliente']);
Route::post('/login/negocio', [LoginController::class, 'loginNegocio']);
Route::post('/login/test', [LoginController::class, 'loginTest']);

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

Route::resource("/cliente", ClienteController::class)->only([
    'store', 'update', 'show', 'destroy', 'index'
]);
Route::get('/cliente/delete/table', [ClienteController::class, 'dropTable']);

//Rutas para el controlador de productos
Route::get('/producto/test/store', [ProductoController::class, 'storeTest']);
Route::get('/producto/create/table', [ProductoController::class, 'createTable']);
Route::get('/producto/delete/table', [ProductoController::class, 'dropTable']);
Route::resource("/producto", ProductoController::class)->only([
    'store', 'update', 'index', 'show', 'destroy'
]);


Route::resource("/pedido", PedidoController::class)->only([
    'store', 'update', 'index', 'show', 'destroy'
]);