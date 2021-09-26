@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Add Class') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('class.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name_numeric" class="col-md-4 col-form-label text-md-right">{{ __('Name numeric') }}</label>

                                <div class="col-md-6">
                                    <input id="name_numeric" type="text" class="form-control @error('name_numeric') is-invalid @enderror" name="name_numeric" value="{{ old('name_numeric') }}" required autocomplete="name_numeric" autofocus>

                                    @error('name_numeric')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('sections:') }}</label>
                                <div class="d-flex justify-content-center">
                                    <div class="form-group">
                                        @foreach($sections as $section)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sections[]" value="{{ $section['id'] }}" id="section{{ $section['id'] }}">
                                                <label class="form-check-label" for="{{ $section['id'] }}">
                                                    {{ $section['name'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>



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
