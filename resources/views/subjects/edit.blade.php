@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Edit Subject') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('subject.update', $subject->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $subject->name }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" class="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('code') }}</label>

                                <div class="col-md-6">
                                    <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ $subject->code }}" required autocomplete="code" autofocus>

                                    @error('code')
                                    <span class="invalid-feedback" class="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="author" class="col-md-4 col-form-label text-md-right">{{ __('author') }}</label>

                                <div class="col-md-6">
                                    <input id="author" type="text" class="form-control @error('author') is-invalid @enderror" name="author" value="{{ $subject->code }}" required autocomplete="author" autofocus>

                                    @error('author')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="class" class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>
                                <div class="col-md-6">
                                    <select name="type" class="form-control @error('type') is-invalid @enderror" id="type" required>
                                        <option selected disabled>
                                            Not Selected
                                        </option>
                                        <option @if($subject->type == 'Theory') selected @endif value="Theory">{{ __('Theory') }}</option>
                                        <option @if($subject->type == 'Practical') selected @endif value="Practical">{{ __('Practical') }}</option>
                                        <option @if($subject->type == 'Optional') selected @endif value="Optional">{{ __('Optional') }}</option>
                                        <option @if($subject->type == 'Mandatory') selected @endif value="Mandatory">{{ __('Mandatory') }}</option>
                                    </select>
                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
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
