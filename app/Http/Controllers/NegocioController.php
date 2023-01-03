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

    public function store(Request $request)
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

            $negocio = new Negocio();
            $negocio->fill($input);
            $negocio->save();
            return $this->sendResponse($negocio, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController store', $e->getMessage(), $e->getCode());
        }
    }

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
            return $this->sendError('UserController storeTest', $e->getMessage(), $e->getCode());
        }
    }

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
            return $this->sendError('UserController storeTest', $e->getMessage(), $e->getCode());
        }
    }

    public function dropTable()
    {
        try {
            
            Schema::dropIfExists('negocios');
            return $this->sendResponse(true, 'Tabla eliminado');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController storeTest', $e->getMessage(), $e->getCode());
        }
    }

}
