<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class PedidoController extends Controller
{
    use ResponseApi;

    public function store(Request $request)
    {
        try {

            if (!Schema::hasTable('pedidos')) {
                $this->createTable();
            }

            $input = $request->all();
            $rules = [
                'producto' => 'required',
                'cantidad' => 'required'
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $pedido = new Pedido();
            $pedido->fill($input);
            $pedido->save();
            return $this->sendResponse($pedido, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController store', $e->getMessage(), $e->getCode());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $input = $request->all();
            $rules = [
                'producto' => 'required',
                'cantidad' => 'required'
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $pedido = Pedido::where('id', $id)->first();
            if (empty($pedido)) throw new Exception('Pedido no encontrado', 404);

            $pedido->fill($input);
            $pedido->save();
            return $this->sendResponse($pedido, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController update', $e->getMessage(), $e->getCode());
        }
    }

    public function index()
    {
        try {
            $pedidos = Pedido::all();
            return $this->sendResponse($pedidos, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController index', $e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {
            $pedido = Pedido::where('id', $id)->first();
            if (empty($pedido)) throw new Exception('Pedido no encontrado', 404);

            return $this->sendResponse($pedido, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController show', $e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            $pedido = Pedido::where('id', $id)->first();
            $pedido->delete();
            return $this->sendResponse($pedido, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController destroy', $e->getMessage(), $e->getCode());
        }
    }

    public function createTable()
    {
        try {
            Schema::create('pedidos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id');
                $table->foreignId('cliente_id');
                $table->string('producto');
                $table->string('cantidad');
                $table->string('fecha del pedido');
                $table->timestamps();
            });
            return $this->sendResponse(true, 'Tabla creada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController createTable', $e->getMessage(), $e->getCode());
        }
    }

    public function dropTable()
    {
        try {
            
            Schema::dropIfExists('pedidos');
            return $this->sendResponse(true, 'Tabla eliminada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController dropTable', $e->getMessage(), $e->getCode());
        }
    }

}
