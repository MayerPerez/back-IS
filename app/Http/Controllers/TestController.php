<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Validator;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseApi;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    use ResponseApi;

    public function __construct()
    {
        
    }

    public function test()
    {
        try {
            
            return $this->sendResponse(true, 'Response');
        } catch (\Exception $e) {
            return $this->sendError('TestController test', $e->getMessage(), $e->getCode());
        }
    }
}
