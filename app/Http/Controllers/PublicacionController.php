<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;


class PublicacionController extends Controller
{
    use ResponseApi;

    //Verifica si la tabla Publicaciones esta creada, si no la crea y Hace un INSERT a la tabla
    public function store(Request $request)
    {
        try {

            if (!Schema::hasTable('publicaciones')) {
                $this->createTable();
            }

            $input = $request->all();
            $rules = [
                'producto' => 'required',
                'cantidad' => 'required'
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $publicacion = new Publicacion();
            $publicacion->fill($input);
            $publicacion->save();
            return $this->sendResponse($publicacion, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController store', $e->getMessage(), $e->getCode());
        }
    }

    //Hace un UPDATE a la Tabla en la fila especificada en el ID, Método POST
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

            $publicacion = Publicacion::where('id', $id)->first();
            if (empty($publicacion)) throw new Exception('Publicacion no encontrado', 404);

            $publicacion->fill($input);
            $publicacion->save();
            return $this->sendResponse($publicacion, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController update', $e->getMessage(), $e->getCode());
        }
    }

    //Muestra todo el contenido de la tabla Publicaciones, Método GET
    public function index()
    {
        try {
            $publicaciones = Publicacion::all();
            return $this->sendResponse($publicaciones, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController index', $e->getMessage(), $e->getCode());
        }
    }

    //Muestra un usuario en especifico usando el ID, Método GET
    public function show($id)
    {
        try {
            $publicacion = Publicacion::where('id', $id)->first();
            if (empty($publicacion)) throw new Exception('Publicacion no encontrado', 404);

            return $this->sendResponse($publicacion, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController show', $e->getMessage(), $e->getCode());
        }
    }

    //ELimina una fila en espeficico usanso el ID, Método DELETE
    public function destroy($id)
    {
        try {
            $publicacion = Publicacion::where('id', $id)->first();
            $publicacion->delete();
            return $this->sendResponse($publicacion, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController destroy', $e->getMessage(), $e->getCode());
        }
    }

    //Crea la tabla publicacion, Método GET
    public function createTable()
    {
        try {
            Schema::create('publicaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id');
                $table->foreignId('cliente_id');
                $table->string('producto');
                $table->string('cantidad');
                $table->timestamps();
            });
            return $this->sendResponse(true, 'Tabla creada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController createTable', $e->getMessage(), $e->getCode());
        }
    }

    //Elimina la tabla Publicacion, Método GET
    public function dropTable()
    {
        try {

            Schema::dropIfExists('publicaciones');
            return $this->sendResponse(true, 'Tabla eliminada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController dropTable', $e->getMessage(), $e->getCode());
        }
    }
}
