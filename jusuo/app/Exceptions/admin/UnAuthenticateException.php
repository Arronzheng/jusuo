<?php

namespace App\Exceptions\admin;

use App\Http\Controllers\ApiRootController;
use App\Http\Services\v1\admin\AuthService;
use App\Services\common\LoginService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Throwable;

class UnAuthenticateException extends Exception
{
    //

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request) {

        return LoginService::handleIfUnAuthenticated($request);

    }
}
