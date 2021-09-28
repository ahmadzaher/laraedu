<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroAreaController extends Controller
{
    public function index()
    {
        return view('frontend.admin.hero');
    }
    public function update(Request $request)
    {
        if ($request->hasFile('hero_photo_url')) {
            //  Let's do everything here
            if ($request->file('hero_photo_url')->isValid()) {
                //
                $validated = $request->validate([
                    'hero_photo_url' => 'mimes:jpeg,png|max:1014',
                ]);
                $extension = $request->hero_photo_url->extension();
                $request->hero_photo_url->storeAs('/public', 'hero_photo'.".".$extension);
                $url = Storage::url('hero_photo'.".".$extension);

                option(['hero_photo_url' => $url]);
            }
        }
        option(['hero_title' => $request->hero_title]);
        option(['button_text' => $request->button_text]);
        option(['button_url' => $request->button_url]);
        option(['hero_description' => $request->hero_description]);
        return redirect('frontend/hero_area')->with('success', 'Updated Successfully');
    }
}
