<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class SettingsController extends Controller
{
    public function general_settings()
    {
        $settings = [
            'default_language' => option('default_language'),
            'default_direction' => option('default_direction'),
            'app_name' => option('app_name'),
            'app_logo' => option('app_logo'),
            'app_favicon' => option('app_favicon'),
        ];
        return response()->json($settings, 200);
    }


    public function update_general_settings(Request $request)
    {
        $request->validate([
            'default_language' => ['required', 'max:255'],
            'default_direction' => ['required', 'max:255'],
            'app_name' => ['required', 'max:255'],
            'app_logo' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'app_favicon' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        if ($request->delete_logo){
            $files = Storage::allFiles('public/images/app_logo');
            Storage::delete($files);
            option(['app_logo' => '']);
        }
        if ($request->delete_favicon){
            $files = Storage::allFiles('public/images/app_favicon');
            Storage::delete($files);
            option(['app_favicon' => '']);
        }
        $files = ['app_logo', 'app_favicon'];
        foreach ($files as $file)
        {
            if ($request->hasFile($file)) {
                if ($request->file($file)->isValid()) {
                    $files = Storage::allFiles('public/images/'.$file);
                    Storage::delete($files);
                    $extension = $request->$file->extension();
                    $request->$file->storeAs('/public/images/'.$file, $file.".".$extension);
                    option([$file => env('APP_URL') . '/storage/images/'.$file . '/' . $file . '.' . $extension]);
                }
            }
        }
        option(['default_language' => $request->default_language]);
        option(['default_direction' => $request->default_direction]);
        option(['app_name' => $request->app_name]);
        return $this->general_settings();
    }
}
