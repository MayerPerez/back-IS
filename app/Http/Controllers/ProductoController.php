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

    //Verifica si la tabla esta creada, en caso contrario la crea y hace un INSERT de un nuevo Producto
    public function store(Request $request)
    {
        try {
            if (!Schema::hasTable('productos')) {
                $this->createTable();
            }

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

    //Hace un UPDATE a la Tabla usando como referencia el ID, Método POST
    public function update(Request $request, $id)
    {
        try {

            $input = $request->all();
            $rules = [
                'nombre' => 'required',
                'cantidad' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $producto = Producto::where('id', $id)->first();
            if (empty($producto)) throw new Exception('Producto no encontrado', 404);

            $producto->fill($input);
            $producto->save();
            return $this->sendResponse($producto, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController update', $e->getMessage(), $e->getCode());
        }
    }

    //Muestra todo el contenido de la tabla Producto, Método GET
    public function index()
    {
        try {
            $productos = Producto::all();
            return $this->sendResponse($productos, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ProductoController index', $e->getMessage(), $e->getCode());
        }
    }

    //Muestra una fila usando como referencia el ID, Método GET
    public function show($id)
    {
        try {
            $producto = Producto::where('id', $id)->first();
            if (empty($producto)) throw new Exception('Prodcuto no encontrado', 404);

            return $this->sendResponse($producto, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ProdcutoController show', $e->getMessage(), $e->getCode());
        }
    }

    //Elimina una fila usando el ID de referencia, Método DELETE
    public function destroy($id)
    {
        try {
            $producto = Producto::where('id', $id)->first();
            $producto->delete();
            return $this->sendResponse($producto, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ProductoController destroy', $e->getMessage(), $e->getCode());
        }
    }

    //test
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

    //Crea la tabla Publicaciones, Método GET
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

    //Elimina la tabla Publicaciones, Método GET
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
