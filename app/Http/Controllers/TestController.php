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

/*
uncomment
extension=pdo_mysql
restart server
*/

class TestController extends Controller
{
    use ResponseApi;

    public function __construct()
    {
        
    }

    public function test()
    {
        try {
            if(DB::connection()->getDatabaseName()) {
                $message = "Yes! successfully connected to the DB: " . DB::connection()->getDatabaseName();
            } else {
                $message = "Error in connection";
            }
            return $this->sendResponse($message, 'Response');
        } catch (\Exception $e) {
            return $this->sendError('TestController test', $e->getMessage(), $e->getCode());
        }
    }

    public function countTables()
    {
        try {
            $count = DB::table('users')->count();
            return $this->sendResponse($count, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('TestController test', $e->getMessage(), $e->getCode());
        }
    }

    public function index()
    {
        try {
            $users = DB::table('users')->get();
            return $this->sendResponse($users, 'Response');
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendError('TestController test', $e->getMessage(), $e->getCode());
        }
    }

    public function store(Request $request)
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
            return $this->sendError('TestController test', $e->getMessage(), $e->getCode());
        }
    }
}
