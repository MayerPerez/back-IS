<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\Publicacion;
use App\Models\Producto;
use App\Models\Negocio;
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

    public function __construct()
    {
        if (!Schema::hasTable('publicaciones')) {
            $this->createTablePub();
        }

        if (!Schema::hasTable('productos')) {
            $this->createTableProd();
        }
    }

    //Verifica si la tabla Publicaciones esta creada, si no la crea y Hace un INSERT a la tabla
    public function store(Request $request)
    {
        try {

            $negocio = $request->user();

            $input = $request->all();
            $rules = [
                'titulo' => 'required',
                'nombre' => 'required',
                'promocion' => 'required',
                'precio' => 'required',
                'cantidad' => 'required',
                'descripcion' => 'required'
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $file = $request->file('image');
            $name = $file->getClientOriginalName();
            
            $path = $request->file('image')->storeAs('public/images', $name);
            
            $url = Storage::url($path);
            
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


    //Hace un UPDATE a la Tabla en la fila especificada en el ID, M??todo POST
    public function update(Request $request, $id)
    {
        try {
            $negocio = $request->user();

            $input = $request->all();
            $rules = [
                'titulo' => 'required',
                'nombre' => 'required',
                'promocion' => 'required',
                'precio' => 'required',
                'cantidad' => 'required',
                'descripcion' => 'required'
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $file = $request->file('image');
            $name = $file->getClientOriginalName();
            
            $path = $request->file('image')->storeAs('public/images', $name);
            
            $url = Storage::url($path);

            $publicacion = Publicacion::where('negocio_id', $negocio->id)->where('id', $id)->first();
            if (empty($publicacion)) throw new Exception('Publicacion no encontrado', 404);

            $publicacion->fill($input);
            $publicacion->disponibilidad = $input['cantidad'];
            $publicacion->pathImage = $url;
            $publicacion->save();

            return $this->sendResponse($publicacion, 'Publicacion editada correctamente');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController update', $e->getMessage(), $e->getCode());
        }
    }

    //Muestra todo el contenido de la tabla Publicaciones, M??todo GET
    public function index()
    {
        try {
            $publicaciones = Publicacion::all();

            //validar horario de negocio

            $filtradas = [];
            foreach ($publicaciones as $publicacion) {
                if(intval($publicacion->disponibilidad) == 0) continue;
                
                $negocio = Negocio::where('id',$publicacion->negocio_id)->first();
                $publicacion->negocio = $negocio->nombre;
                $publicacion->direccion = $negocio->direccion;
                $publicacion->horario_c = $negocio->horario_c;
                
                array_push($filtradas, $publicacion );
            }

            return $this->sendResponse($filtradas, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController index', $e->getMessage(), $e->getCode());
        }
    }

    //Muestra un usuario en especifico usando el ID, M??todo GET
    public function show(Request $request, $id)
    {
        try {
            $negocio = $request->user();
            $publicacion = Publicacion::where('negocio_id', $negocio->id)->where('id', $id)->first();
            if (empty($publicacion)) throw new Exception('Publicacion no encontrado', 404);

            return $this->sendResponse($publicacion, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController show', $e->getMessage(), $e->getCode());
        }
    }

    //ELimina una fila en espeficico usanso el ID, M??todo DELETE
    public function destroy(Request $request, $id)
    {
        try {
            $negocio = $request->user();
            $publicacion = Publicacion::where('negocio_id', $negocio->id)->where('id', $id)->first();
            $publicacion->delete();
            return $this->sendResponse($publicacion, 'Publicacion eliminada');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('PublicacionController destroy', $e->getMessage(), $e->getCode());
        }
    }

    //Crea la tabla publicacion, M??todo GET
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

    //Crea la tabla Publicaciones, M??todo GET
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

    //Elimina la tabla Publicacion, M??todo GET
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
