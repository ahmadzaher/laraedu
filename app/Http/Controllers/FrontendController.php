<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function __construct()
    {
        if(!option('active'))
        {
            return redirect('/dashboard');
        }
    }
    public function index()
    {
        return view('frontend.index');
    }
}
