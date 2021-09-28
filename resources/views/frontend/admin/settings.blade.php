@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Frontend Settings') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('frontend_settings.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="cms_title" class="col-md-4 col-form-label text-md-right">{{ __('CMS Title') }}</label>

                                <div class="col-md-6">
                                    <input id="cms_title" type="text" class="form-control @error('cms_title') is-invalid @enderror" name="cms_title" value="{{ option('cms_title') }}" autocomplete="cms_title" autofocus>

                                    @error('cms_title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">

                                <label for="active" class="col-md-4 col-form-label text-md-right">{{ __('CMS Frontend:') }}</label>
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input value="1" type="radio" id="customRadio1" name="active" class="custom-control-input" @if(option('active')) checked @endif>
                                        <label class="custom-control-label" for="customRadio1">{{ __('Enabled') }}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input value="0" type="radio" id="customRadio2" name="active" class="custom-control-input" @if(!option('active')) checked @endif>
                                        <label class="custom-control-label" for="customRadio2">{{ __('Disabled') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="receive_email_to" class="col-md-4 col-form-label text-md-right">{{ __('Receive Email to') }}</label>

                                <div class="col-md-6">
                                    <input id="receive_email_to" type="text" class="form-control @error('receive_email_to') is-invalid @enderror" name="receive_email_to" value="{{ option('receive_email_to') }}" autocomplete="receive_email_to" autofocus>

                                    @error('receive_email_to')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="number_of_working_hours" class="col-md-4 col-form-label text-md-right">{{ __('Working Hours') }}</label>

                                <div class="col-md-6">
                                    <input id="number_of_working_hours" type="text" class="form-control @error('number_of_working_hours') is-invalid @enderror" name="number_of_working_hours" value="{{ option('number_of_working_hours') }}" autocomplete="number_of_working_hours" autofocus>

                                    @error('number_of_working_hours')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone_number" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>

                                <div class="col-md-6">
                                    <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ option('phone_number') }}" autocomplete="phone_number" autofocus>

                                    @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ option('email') }}" autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="fax" class="col-md-4 col-form-label text-md-right">{{ __('Fax') }}</label>

                                <div class="col-md-6">
                                    <input id="fax" type="text" class="form-control @error('fax') is-invalid @enderror" name="fax" value="{{ option('fax') }}" autocomplete="fax" autofocus>

                                    @error('fax')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="footer_about_text" class="col-md-4 col-form-label text-md-right">{{ __('Footer About Text') }}</label>

                                <div class="col-md-6">
                                    <textarea id="footer_about_text" type="text" class="form-control @error('footer_about_text') is-invalid @enderror" name="footer_about_text" autocomplete="footer_about_text" autofocus>{{ option('footer_about_text') }}</textarea>

                                    @error('footer_about_text')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="copyright_text" class="col-md-4 col-form-label text-md-right">{{ __('Copyright Text') }}</label>

                                <div class="col-md-6">
                                    <textarea id="copyright_text" type="text" class="form-control @error('copyright_text') is-invalid @enderror" name="copyright_text" autocomplete="copyright_text" autofocus>{{ option('copyright_text') }}</textarea>

                                    @error('copyright_text')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="facebook_url" class="col-md-4 col-form-label text-md-right">{{ __('Facebook URL') }}</label>

                                <div class="col-md-6">
                                    <input id="facebook_url" type="text" class="form-control @error('facebook_url') is-invalid @enderror" name="facebook_url" value="{{ option('facebook_url') }}" autocomplete="facebook_url" autofocus>

                                    @error('facebook_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="twitter_url" class="col-md-4 col-form-label text-md-right">{{ __('Twitter URL') }}</label>

                                <div class="col-md-6">
                                    <input id="twitter_url" type="text" class="form-control @error('twitter_url') is-invalid @enderror" name="twitter_url" value="{{ option('twitter_url') }}" autocomplete="twitter_url" autofocus>

                                    @error('twitter_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="youtube_url" class="col-md-4 col-form-label text-md-right">{{ __('Youtube URL') }}</label>

                                <div class="col-md-6">
                                    <input id="youtube_url" type="text" class="form-control @error('youtube_url') is-invalid @enderror" name="youtube_url" value="{{ option('youtube_url') }}" autocomplete="youtube_url" autofocus>

                                    @error('youtube_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="google_url" class="col-md-4 col-form-label text-md-right">{{ __('Google URL') }}</label>

                                <div class="col-md-6">
                                    <input id="google_url" type="text" class="form-control @error('google_url') is-invalid @enderror" name="google_url" value="{{ option('google_url') }}" autocomplete="google_url" autofocus>

                                    @error('google_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="linkedin_url" class="col-md-4 col-form-label text-md-right">{{ __('Linkedin URL') }}</label>

                                <div class="col-md-6">
                                    <input id="linkedin_url" type="text" class="form-control @error('linkedin_url') is-invalid @enderror" name="linkedin_url" value="{{ option('linkedin_url') }}" autocomplete="linkedin_url" autofocus>

                                    @error('linkedin_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="pinterest_url" class="col-md-4 col-form-label text-md-right">{{ __('Pinterest URL') }}</label>

                                <div class="col-md-6">
                                    <input id="pinterest_url" type="text" class="form-control @error('pinterest_url') is-invalid @enderror" name="pinterest_url" value="{{ option('pinterest_url') }}" autocomplete="pinterest_url" autofocus>

                                    @error('pinterest_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="instagram_url" class="col-md-4 col-form-label text-md-right">{{ __('Instagram URL') }}</label>

                                <div class="col-md-6">
                                    <input id="instagram_url" type="text" class="form-control @error('instagram_url') is-invalid @enderror" name="instagram_url" value="{{ option('instagram_url') }}" autocomplete="instagram_url" autofocus>

                                    @error('instagram_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

{{--                            --}}
{{--                            --}}
{{--                            --}}
{{--                            logo--}}
{{--                            fav_icon--}}
{{--                            --}}
{{--                            --}}
{{--                            --}}
{{--                            --}}
                            <hr />

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
