<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PublicacionController;
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

//Rutas para el controlador de pruebas
Route::post('/test/store', [TestController::class, 'store']);
Route::get('/test/index', [TestController::class, 'index']);
Route::get('/test/count', [TestController::class, 'countTables']);

Route::post('/cliente', [ClienteController::class, 'store']);
Route::post('/negocio', [NegocioController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    //Rutas para el controlador de negocios
    Route::get('/negocio/auth', [NegocioController::class, 'authNegocio']);
    Route::put('/negocio', [NegocioController::class, 'updateAuth']);
    Route::resource("/negocio", NegocioController::class)->only([
        'store', 'update', 'show', 'destroy', 'index'
    ]);
    Route::get('/negocio/test/store', [NegocioController::class, 'storeTest']);
    Route::get('/negocio/create/table', [NegocioController::class, 'createTable']);
    Route::get('/negocio/delete/table', [NegocioController::class, 'dropTable']);

    Route::get('/cliente/auth', [ClienteController::class, 'authCliente']);
    Route::put('/cliente', [ClienteController::class, 'updateAuth']);
    Route::resource("/cliente", ClienteController::class)->only([
        'update', 'show', 'destroy', 'index'
    ]);
    Route::get('/cliente/delete/table', [ClienteController::class, 'dropTable']);

    //Rutas para el controlador de productos
    Route::get('/producto/test/store', [ProductoController::class, 'storeTest']);
    Route::get('/producto/create/table', [ProductoController::class, 'createTable']);
    Route::get('/producto/delete/table', [ProductoController::class, 'dropTable']);
    Route::resource("/producto", ProductoController::class)->only([
        'store', 'update', 'index', 'show', 'destroy'
    ]);

    Route::post('/publicacion', [PublicacionController::class, 'store']);
    Route::get('/publicaciones', [PublicacionController::class, 'getPublicaciones']);
    Route::post('/publicacion/{id}', [PublicacionController::class, 'update']);
    Route::resource("/publicacion", PublicacionController::class)->only([
        'show', 'destroy', 'index'
    ]);

    //Rutas para el controlador de pedidos
    Route::resource("/pedido", PedidoController::class)->only([
        'store', 'update', 'index', 'show', 'destroy'
    ]);
});

