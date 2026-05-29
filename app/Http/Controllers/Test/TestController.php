<?php

namespace App\Http\Controllers\Test;

use App\Classes\AuthContext;
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
        // $dataUser = auth()->user()->companyUsers()->with('company')->get();
        // $dataUser = $this->User->getDataUser(auth('api')->user());

        $dataUser['user'] = AuthContext::user();
        $dataUser['companies'] = AuthContext::getCompanies();

        $dataUser['stores'] = AuthContext::stores();

        return response()->json($dataUser);
    }
}
