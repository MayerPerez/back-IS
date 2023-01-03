<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    use ResponseApi;

    public function index()
    {
        try {
            $users = User::all();
            return $this->sendResponse($users, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController index', $e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {
            $user = User::where('id', $id)->first();
            return $this->sendResponse($user, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController index', $e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::where('id', $id)->first();
            $user->delete();
            return $this->sendResponse($user, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController index', $e->getMessage(), $e->getCode());
        }
    }

    public function store(Request $request)
    {
        try {

            $input = $request->all();
            $rules = [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required|min:6',
            ];

            $validator = Validator::make($input, $rules);
            if ($validator->fails()) return $this->sendError('Error de validacion', $validator->errors()->all(), 422);

            $user = new User();
            $user->fill($input);
            $user->save();
            return $this->sendResponse($user, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController store', $e->getMessage(), $e->getCode());
        }
    }

    public function storeTest(Request $request)
    {
        try {
            $user = new User();
            $user->name = "Prueba1";
            $user->email = "prueba@test.com";
            $user->password = "prueba@test.com";
            $user->save();
            return $this->sendResponse($user, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('UserController store', $e->getMessage(), $e->getCode());
        }
    }
}
