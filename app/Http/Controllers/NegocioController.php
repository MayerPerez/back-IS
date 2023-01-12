<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\Negocio;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class NegocioController extends Controller
{
    use ResponseApi;

    //Funcion que ve si existe la tabla de Negocio, sino la crea, en caso de que si crea hace un insert a la tabla de Negocios Método POST
    public function store(Request $request)
    {
        try {

            if (!Schema::hasTable('negocios')) {
                $this->createTable();
            }

            $input = $request->all();
            $rules = [
                'nombre' => 'required',
                'correo' => 'required',
                'password' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
                'horario' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $negocio = new Negocio();
            $negocio->fill($input);
            $negocio->save();
            return $this->sendResponse($negocio, 'Negocio Creado');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController store', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que hace un update Método POST PUT
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
                'horario' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $negocio = Negocio::where('id', $id)->first();
            if (empty($negocio)) throw new Exception('Negocio no encontrado', 404);

            $negocio->fill($input);
            $negocio->save();
            return $this->sendResponse($negocio, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController update', $e->getMessage(), $e->getCode());
        }
    }

    public function updateAuth(Request $request)
    {
        try {

            $negocio = $request->user();

            $input = $request->all();
            $rules = [
                'nombre' => 'required',
                'correo' => 'required',
                'password' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
                'horario' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            /*$negocio = Negocio::where('id', $id)->first();
            if (empty($negocio)) throw new Exception('Negocio no encontrado', 404);*/

            $negocio->fill($input);
            $negocio->save();
            return $this->sendResponse($negocio, 'Información actualizada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController update', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que retorna todos los elementos de la tabla negocios Método GET
    public function index()
    {
        try {
            $negocios = Negocio::all();
            return $this->sendResponse($negocios, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController index', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que retorna muestra un elemento en específico usando su ID Método GET
    public function show($id)
    {
        try {
            $negocio = Negocio::where('id', $id)->first();
            if (empty($negocio)) throw new Exception('Negocio no encontrado', 404);

            return $this->sendResponse($negocio, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController show', $e->getMessage(), $e->getCode());
        }
    }

    public function authNegocio(Request $request)
    {
        try {

            $negocio = $request->user();

            return $this->sendResponse($negocio, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController show', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que borra un elemento en especifico usando su ID, Método DELETE
    public function destroy($id)
    {
        try {
            $negocio = Negocio::where('id', $id)->first();
            $negocio->delete();
            return $this->sendResponse($negocio, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController destroy', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion de prueba para crear un usaurio en la base de datos de forma manual
    public function storeTest(Request $request)
    {
        try {
            $negocio = new Negocio();
            $negocio->nombre = "NegocioTest";
            $negocio->correo = "negocio@test.com";
            $negocio->password = "1234567";
            $negocio->telefono = "1234567890";
            $negocio->direccion = "Calle San Marcos";
            $negocio->horario = "9:00 am - 9:00 pm";
            $negocio->save();
            return $this->sendResponse($negocio, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController storeTest', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que crea la tabla de negocios, Método GET
    public function createTable()
    {
        try {
            Schema::create('negocios', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('correo')->unique();
                $table->string('password');
                $table->string('telefono');
                $table->string('direccion');
                $table->string('horario');
                $table->timestamps();
            });
            return $this->sendResponse(true, 'Tabla creada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController createTable', $e->getMessage(), $e->getCode());
        }
    }

    //Funcion que elimina la tabla de negocios, Método GET
    public function dropTable()
    {
        try {

            Schema::dropIfExists('negocios');
            return $this->sendResponse(true, 'Tabla eliminada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('NegocioController dropTable', $e->getMessage(), $e->getCode());
        }
    }
}
