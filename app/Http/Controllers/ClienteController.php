<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class ClienteController extends Controller
{
    use ResponseApi;

    public function __construct()
    {
        if (!Schema::hasTable('clientes')) {
            $this->createTable();
        }
    }

    //Crea la base de datos si no existe y si existe crea una fila mpetodo POST
    public function store(Request $request)
    {
        try {

            $input = $request->all();
            $rules = [
                'nombre' => 'required',
                'correo' => 'required|unique:App\Models\Cliente,correo',
                'password' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $cliente = new Cliente();
            $cliente->fill($input);
            $cliente->save();
            return $this->sendResponse($cliente, 'Cuenta creada correctamente');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController store', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion para actualizar los datos de la base de datos Método POST PUT
    public function update(Request $request, $id)
    {
        try {

            $input = $request->all();
            $rules = [
                'nombre' => 'required',
                'correo' => 'required',
                'password' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $cliente = Cliente::where('id', $id)->first();
            if (empty($cliente)) throw new Exception('Cliente no encontrado', 404);

            $cliente->fill($input);
            $cliente->save();
            return $this->sendResponse($cliente, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController update', $e->getMessage(), $e->getCode());
        }
    }

    public function updateAuth(Request $request)
    {
        try {

            $cliente = $request->user();

            $input = $request->all();
            $rules = [
                'nombre' => 'required',
                'correo' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $cliente->fill($input);
            $cliente->save();
            return $this->sendResponse($cliente, 'Información actalizada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController update', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que retorna todos los elementos de la tabla clientes Método GET
    public function index()
    {
        try {
            $clientes = Cliente::all();
            return $this->sendResponse($clientes, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController index', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que muestra un cliente en específico usando su ID Método GET
    public function show($id)
    {
        try {
            $cliente = Cliente::where('id', $id)->first();
            if (empty($cliente)) throw new Exception('Cliente no encontrado', 404);

            return $this->sendResponse($cliente, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController show', $e->getMessage(), $e->getCode());
        }
    }

    public function authCliente(Request $request)
    {
        try {

            $cliente = $request->user();

            return $this->sendResponse($cliente, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController show', $e->getMessage(), $e->getCode());
        }
    }

    //Fncion que elimina un cliente en especifico Método DELETE
    public function destroy($id)
    {
        try {
            $cliente = Cliente::where('id', $id)->first();
            $cliente->delete();
            return $this->sendResponse($cliente, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController destroy', $e->getMessage(), $e->getCode());
        }
    }
    //Funcion que crea un usuario de prueba, se uso para la conexion de la base de datos
    public function storeTest(Request $request)
    {
        try {
            $cliente = new Cliente();
            $cliente->nombre = "NegocioTest";
            $cliente->correo = "negocio@test.com";
            $cliente->password = "1234567";
            $cliente->telefono = "1234567890";
            $cliente->direccion = "Calle San Marcos";
            $cliente->save();
            return $this->sendResponse($cliente, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController storeTest', $e->getMessage(), $e->getCode());
        }
    }
    //Funcion que crea la base de datos de los clientes
    public function createTable()
    {
        try {
            Schema::create('clientes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('correo')->unique();
                $table->string('password');
                $table->string('telefono');
                $table->string('direccion');
                $table->timestamps();
            });
            return $this->sendResponse(true, 'Tabla creada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController createTable', $e->getMessage(), $e->getCode());
        }
    }
    //Funcion que elimina la base de datos de clientes
    public function dropTable()
    {
        try {

            Schema::dropIfExists('clientes');
            return $this->sendResponse(true, 'Tabla eliminada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController dropTable', $e->getMessage(), $e->getCode());
        }
    }
}
