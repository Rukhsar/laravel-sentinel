<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class PagesController extends Controller
{
    public function getCustomer()
    {
        return view('pages.customer');
    }

    public function getAdmin()
    {
        return view('pages.admin');
    }
}
