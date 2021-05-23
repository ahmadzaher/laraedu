@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Edit section') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('section.update', $section->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $section->name }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" class="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="capacity" class="col-md-4 col-form-label text-md-right">{{ __('Name numeric') }}</label>

                                <div class="col-md-6">
                                    <input id="capacity" type="text" class="form-control @error('capacity') is-invalid @enderror" name="capacity" value="{{ $section->capacity }}" required autocomplete="capacity" autofocus>

                                    @error('capacity')
                                    <span class="invalid-feedback" class="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
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
