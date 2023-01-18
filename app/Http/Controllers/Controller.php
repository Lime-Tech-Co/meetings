<?php

namespace App\Http\Controllers;

use App\Http\Contracts\ReturnValues;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected ReturnValues $returner;

    public function __construct()
    {
        $this->returner = new ReturnValues;
    }
}
