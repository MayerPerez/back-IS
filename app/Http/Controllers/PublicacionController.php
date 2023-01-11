<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\Publicacion;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;


class PublicacionController extends Controller
{
    use ResponseApi;

    //Verifica si la tabla Publicaciones esta creada, si no la crea y Hace un INSERT a la tabla
    public function store(Request $request)
    {
        try {

            $negocio = $request->user();

            if (!Schema::hasTable('publicaciones')) {
                $this->createTablePub();
            }

            if (!Schema::hasTable('productos')) {
                $this->createTableProd();
            }

            $input = $request->all();
            $rules = [
                'titulo' => 'required',
                'nombre' => 'required',
                'promocion' => 'required',
                'precio' => 'required',
                'cantidad' => 'required',
                'descripcion' => 'required'
            ];

            $file = $request->file('image');
            $name = $file->getClientOriginalName();
            
            $path = $request->file('image')->storeAs('public/images', $name);
            
            $url = Storage::url($path);
            
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $publicacion = new Publicacion();
            $publicacion->fill($input);
            $publicacion->negocio_id = $negocio->id;
            $publicacion->disponibilidad = $input['cantidad'];
            $publicacion->pathImage = $url;

            $producto = new Producto();
            $producto->fill($input);
            $producto->save();

            $publicacion->producto_id = $producto->id;
            $publicacion->save();
            return $this->sendResponse($publicacion, 'Producto agregado correctamente');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController store', $e->getMessage(), $e->getCode());
        }
    }


    public function getPublicaciones(Request $request)
    {
        try {
            $negocio = $request->user();

            $publicaciones = Publicacion::where('negocio_id', $negocio->id)->get();

            return $this->sendResponse($publicaciones, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController index', $e->getMessage(), $e->getCode());
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
            return $this->sendResponse($publicacion, 'Publicacion eliminada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController destroy', $e->getMessage(), $e->getCode());
        }
    }

    //Crea la tabla publicacion, Método GET
    public function createTablePub()
    {
        try {
            Schema::create('publicaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id');
                $table->foreignId('negocio_id');
                $table->string('titulo');
                $table->string('nombre');
                $table->string('descripcion');
                $table->string('promocion');
                $table->string('precio');
                $table->string('disponibilidad');
                $table->string('pathImage');
                $table->timestamps();
            });
            return $this->sendResponse(true, 'Tabla creada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController createTable', $e->getMessage(), $e->getCode());
        }
    }

    //Crea la tabla Publicaciones, Método GET
    public function createTableProd()
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
