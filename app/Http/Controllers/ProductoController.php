<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class ProductoController extends Controller
{
    use ResponseApi;

    public function store(Request $request)
    {
        try {

            $input = $request->all();
            $rules = [
                'nombre' => 'required',
                'cantidad' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $producto = new Producto();
            $producto->fill($input);
            $producto->save();
            return $this->sendResponse($producto, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController store', $e->getMessage(), $e->getCode());
        }
    }

    public function storeTest(Request $request)
    {
        try {
            $producto = new Producto();
            $producto->nombre = "ProdcutoTest";
            $producto->cantidad = "12";
            $producto->save();
            return $this->sendResponse($producto, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController storeTest', $e->getMessage(), $e->getCode());
        }
    }

    public function createTable()
    {
        try {
            Schema::create('productos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('cantidad');
                $table->timestamps();
            });
            return $this->sendResponse(true, 'Tabla creada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController storeTest', $e->getMessage(), $e->getCode());
        }
    }

    public function dropTable()
    {
        try {

            Schema::dropIfExists('productos');
            return $this->sendResponse(true, 'Tabla eliminado');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController storeTest', $e->getMessage(), $e->getCode());
        }
    }
}
