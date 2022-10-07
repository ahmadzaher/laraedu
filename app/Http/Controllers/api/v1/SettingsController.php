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
            'company_name' => option('company_name'),
            'app_logo' => option('app_logo'),
            'app_favicon' => option('app_favicon'),
            'facebook_client_id' => option('facebook_client_id'),
            'facebook_client_secret' => option('facebook_client_secret'),
            'receive_email_to' => option('receive_email_to'),
            'number_of_working_hours' => option('number_of_working_hours'),
            'phone_number' => option('phone_number'),
            'email' => option('email'),
            'fax' => option('fax'),
            'footer_about_text' => option('footer_about_text'),
            'copyright_text' => option('copyright_text'),
            'facebook_url' => option('facebook_url'),
            'twitter_url' => option('twitter_url'),
            'youtube_url' => option('youtube_url'),
            'google_url' => option('google_url'),
            'linkedin_url' => option('linkedin_url'),
            'pinterest_url' => option('pinterest_url'),
            'instagram_url' => option('instagram_url'),
            'google_client_id' => option('google_client_id'),
            'google_client_secret' => option('google_client_secret'),
            'privacy_policy' => option('privacy_policy'),
            'usage_policy' => option('usage_policy'),
            'app_version' => option('app_version'),
            'app_disabled' => option('app_disabled'),
        ];
        return response()->json($settings, 200);
    }


    public function update_general_settings(Request $request)
    {
        $request->validate([
            'default_language' => ['required', 'max:255'],
            'default_direction' => ['required', 'max:255'],
            'app_name' => ['required', 'max:255'],
            'company_name' => ['required', 'max:255'],
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
                    option([$file => env('APP_URL') . '/public/storage/images/'.$file . '/' . $file . '.' . $extension]);
                }
            }
        }
        option(['default_language' => $request->default_language]);
        option(['default_direction' => $request->default_direction]);
        option(['app_name' => $request->app_name]);
        option(['company_name' => $request->company_name]);
        option(['facebook_client_id' => $request->facebook_client_id]);
        option(['facebook_client_secret' => $request->facebook_client_secret]);
        option(['receive_email_to' => $request->receive_email_to]);
        option(['number_of_working_hours' => $request->number_of_working_hours]);
        option(['phone_number' => $request->phone_number]);
        option(['email' => $request->email]);
        option(['fax' => $request->fax]);
        option(['footer_about_text' => $request->footer_about_text]);
        option(['copyright_text' => $request->copyright_text]);
        option(['facebook_url' => $request->facebook_url]);
        option(['twitter_url' => $request->twitter_url]);
        option(['youtube_url' => $request->youtube_url]);
        option(['google_url' => $request->google_url]);
        option(['linkedin_url' => $request->linkedin_url]);
        option(['pinterest_url' => $request->pinterest_url]);
        option(['instagram_url' => $request->instagram_url]);
        option(['google_client_id' => $request->google_client_id]);
        option(['google_client_secret' => $request->google_client_secret]);
        option(['privacy_policy' => $request->privacy_policy]);
        option(['usage_policy' => $request->usage_policy]);
        option(['app_version' => $request->app_version]);
        option(['app_disabled' => $request->app_disabled]);
        return $this->general_settings();
    }
}
