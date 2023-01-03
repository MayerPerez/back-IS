<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\Cliente;
use App\Models\Negocio;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class LoginController extends Controller
{
    use ResponseApi;

    public function loginCliente(Request $request)
    {
        try {
            $input = $request->all();
            $rules = [
                'correo' => 'required',
                'password' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $cliente = Cliente::where('correo', $input['correo'])->first();

            if (empty($cliente)) throw new Exception('Usario no encontrado', 404);
            
            if(!Hash::check($input['password'], $cliente->password))  throw new Exception('Credenciales incorrectas', 404);
            
            return $this->sendResponse($cliente, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('LoginController storeTest', $e->getMessage(), $e->getCode());
        }
    }

    public function loginNegocio(Request $request)
    {
        try {
            $input = $request->all();
            $rules = [
                'correo' => 'required',
                'password' => 'required',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $negocio = Negocio::where('correo', $input['correo'])->first();

            if (empty($negocio)) throw new Exception('Negocio no encontrado', 404);
            
            if(!Hash::check($input['password'], $negocio->password))  throw new Exception('Credenciales incorrectas', 404);
            
            return $this->sendResponse($negocio, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('LoginController storeTest', $e->getMessage(), $e->getCode());
        }
    }
}
