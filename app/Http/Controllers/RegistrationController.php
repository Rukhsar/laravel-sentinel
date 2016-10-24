<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class RegistrationController extends Controller
{
    public function getRegister()
    {
        return view('auth.register');
    }
}
