@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Frontend Settings') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('hero_area.update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label for="hero_title" class="col-md-4 col-form-label text-md-right">{{ __('Title') }}</label>

                                <div class="col-md-6">
                                    <input id="hero_title" type="text" class="form-control @error('hero_title') is-invalid @enderror" name="hero_title" value="{{ option('hero_title') }}" autocomplete="hero_title" autofocus>

                                    @error('hero_title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="hero_description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                                <div class="col-md-6">
                                    <textarea id="hero_description" type="text" class="form-control @error('hero_description') is-invalid @enderror" name="hero_description" autocomplete="hero_description" autofocus>{{ option('hero_description') }}</textarea>

                                    @error('hero_description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="button_text" class="col-md-4 col-form-label text-md-right">{{ __('Button Text') }}</label>

                                <div class="col-md-6">
                                    <input id="button_text" type="text" class="form-control @error('button_text') is-invalid @enderror" name="button_text" value="{{ option('button_text') }}" autocomplete="button_text" autofocus>

                                    @error('button_text')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="button_url" class="col-md-4 col-form-label text-md-right">{{ __('Button URL') }}</label>

                                <div class="col-md-6">
                                    <input id="button_url" type="text" class="form-control @error('button_url') is-invalid @enderror" name="button_url" value="{{ option('button_url') }}" autocomplete="button_url" autofocus>

                                    @error('button_url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="hero_photo_url" class="col-md-4 col-form-label text-md-right">{{ __('Photo') }}</label>
                                <div class="col-md-6">
                                        <input id="hero_photo_url" type="file" name="hero_photo_url">
                                </div>
                            </div>
{{--                            hero_photo--}}
{{--                            hero_photo_url--}}
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
