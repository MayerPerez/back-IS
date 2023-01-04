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

    public function store(Request $request)
    {
        try {

            if (!Schema::hasTable('clientes')) {
                $this->createTable();
            }

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
            return $this->sendResponse($cliente, 'Cliente creado correctamente');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('ClienteController store', $e->getMessage(), $e->getCode());
        }
    }

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
