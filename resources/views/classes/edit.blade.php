@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Edit class') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('class.update', $class->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $class->name }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" class="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="name_numeric" class="col-md-4 col-form-label text-md-right">{{ __('Name numeric') }}</label>

                                <div class="col-md-6">
                                    <input id="name_numeric" type="text" class="form-control @error('name_numeric') is-invalid @enderror" name="name_numeric" value="{{ $class->name_numeric }}" required autocomplete="name_numeric" autofocus>

                                    @error('name_numeric')
                                    <span class="invalid-feedback" class="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <hr />
                            <div class="form-group row">
                                <label for="sections" class="col-md-4 col-form-label text-md-right">{{ __('Sections:') }}</label>
                                <div class="d-flex justify-content-center">
                                    <div class="form-group">
                                        @foreach($sections as $section)
                                            <div class="form-check">
                                                <input
                                                    @if(in_array($section->id, $class_sections))
                                                    checked
                                                    @endif
                                                    class="form-check-input" type="checkbox" name="sections[]" value="{{ $section->id }}" id="section{{ $section->id }}">
                                                <label class="form-check-label" for="{{ $section->id }}">
                                                    {{ $section->name }}
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
