<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use Carbon\Carbon;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Negocio;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class PedidoController extends Controller
{
    use ResponseApi;

    public function __construct()
    {
        if (!Schema::hasTable('pedidos')) {
            $this->createTable();
        }
    }
    //VErifica si esta creada una tabla, la crea en caso de que no y hace un insert de una fila nueva
    public function store(Request $request)
    {
        try {

            $cliente = $request->user();

            $input = $request->all();
            $rules = [
                'publicacion_id' => 'required',
                'negocio_id' => 'required',
                'cantidad' => 'required'
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            //Validar horario

            //Validar cantidad
            $publicacion = Publicacion::where('id', $input['publicacion_id'])
                    ->where('negocio_id', $input['negocio_id'])
                    ->first();

            if(empty($publicacion)) return $this->sendError('Not Found', ['Publicacion no encontrada'], 404);

            $cantidad = intval($input['cantidad']);
            if(intval($publicacion->disponibilidad) < $cantidad) return $this->sendError('Error', ['¡Cantidad excedente!. Refresca la pagina para ver la cantidad disponible actual'], 404);

            $pedido = new Pedido();
            $pedido->fill($input);
            $pedido->cliente_id = $cliente->id;
            $pedido->status = 'Reservado';
            $pedido->save();

            $publicacion->disponibilidad = strval(intval($publicacion->disponibilidad) - $cantidad);
            $publicacion->save();

            return $this->sendResponse($pedido, 'Producto reservado. ¡Tienes 30 minutos para ir a recogerlo! Si no llegas se cancelará tu reservación.');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController store', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que hace un UPDATE a los datos de una fila en específico usando su ID, Método POST
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

    //Funcion que retorna todo el contenido de la tabla Pedidos, Método GET
    public function indexCliente(Request $request)
    {
        try {
            $cliente = $request->user();
            $pedidos = Pedido::where('cliente_id', $cliente->id)->where('status', 'Reservado')->get();
            foreach ($pedidos as $pedido) {
                $negocio = Negocio::where('id',$pedido->negocio_id)->first();
                $publicacion = Publicacion::where('id', $pedido->publicacion_id)
                ->where('negocio_id', $pedido->negocio_id)
                ->first();

                $pedido->producto = $publicacion->nombre;
                $pedido->precio = $publicacion->precio;
                $pedido->negocio = $negocio->nombre;
                $pedido->direccion = $negocio->direccion;
                $tiempo = new Carbon($pedido->created_at);
                $tiempo->setTimezone('America/Mexico_City');
                $pedido->hora = $tiempo->toTimeString();
                $pedido->fecha = $tiempo->toDateString();
                $pedido->total = strval(intval($pedido->cantidad)* intval($publicacion->precio));
            }

            return $this->sendResponse($pedidos, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController index', $e->getMessage(), $e->getCode());
        }
    }

    public function indexNegocio(Request $request)
    {
        try {
            $negocio = $request->user();
            $pedidos = Pedido::where('negocio_id', $negocio->id)->where('status', 'Reservado')->get();
            foreach ($pedidos as $pedido) {
                $cliente = Cliente::where('id',$pedido->cliente_id)->first();
                $publicacion = Publicacion::where('id', $pedido->publicacion_id)->first();

                $pedido->cliente = $cliente->nombre;
                $pedido->telefono = $cliente->telefono;
                $pedido->producto = $publicacion->nombre;
                $pedido->precio = $publicacion->precio;
                $tiempo = new Carbon($pedido->created_at);
                $tiempo->setTimezone('America/Mexico_City');
                $pedido->hora = $tiempo->toTimeString();
                $pedido->fecha = $tiempo->toDateString();
                $pedido->total = strval(intval($pedido->cantidad)* intval($publicacion->precio));
            }

            return $this->sendResponse($pedidos, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController index', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que  muestra una fila en espeficico usando su ID Métdo GET
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

    //Funcion que elimina una fila en especifico usando su ID, Método DELETE
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

    //Funcion que crea la tabla de Pedidos, Método  GET
    public function createTable()
    {
        try {
            Schema::create('pedidos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('publicacion_id');
                $table->foreignId('cliente_id');
                $table->string('negocio_id');
                $table->string('cantidad');
                $table->string('status');
                $table->timestamps();
            });
            return $this->sendResponse(true, 'Tabla creada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PedidoController createTable', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que elimina la tabla de pedidos, Método Get
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
