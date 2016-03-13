<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GradeController extends Controller
{
    //Apply middleware
    public function __construct()
    {
        //Require JWT token for all functions
        $this->middleware("jwt.auth");
    }


}
