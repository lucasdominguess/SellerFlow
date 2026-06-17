<?php

namespace App\Http\Controllers\Test;

use App\Classes\AuthContext;
use App\Classes\Sku;
use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Http\Controllers\Controller;



class TestController extends Controller
{

    public function __construct(
        public UserRepositoryInterface $User
        ) {
            }
    public function test()
    {

        $sku =Sku::generate('reparador de pontas f2');

        return response()->json($sku);
    }
}
