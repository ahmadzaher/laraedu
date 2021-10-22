<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\SchoolClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{

    public function getClasses()
    {
        $classes = SchoolClass::all();
        return response()->json($classes, 200);
    }
}
