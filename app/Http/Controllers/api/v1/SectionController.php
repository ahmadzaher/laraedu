<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\SchoolSection;
use Illuminate\Http\Request;

class SectionController extends Controller
{

    public function getSections()
    {
        $sections = SchoolSection::latest()->get();
        return response()->json($sections, 200);
    }
}
