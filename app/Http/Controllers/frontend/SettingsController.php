<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('frontend.admin.settings');
    }
    public function update(Request $request)
    {
        if ($request->hasFile('logo')) {
            if ($request->file('logo')->isValid()) {
                $validated = $request->validate([
                    'logo' => 'mimes:jpeg,png,jpg|max:1024',
                ]);
                if($validated)
                {
                    $extension = $request->logo->extension();
                    $request->logo->storeAs('/public', 'logo'.".".$extension);
                    $url = Storage::url('logo'.".".$extension);

                    option(['logo' => $url]);
                }
            }
        }
        option(['cms_title' => $request->cms_title]);
        option(['active' => $request->active]);
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
        return redirect('frontend/settings')->with('success', 'Updated Successfully');
    }
}
