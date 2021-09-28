@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Edit Exam Grade') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('exam_grade.update', $exam_grade->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $exam_grade->name }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="point" class="col-md-4 col-form-label text-md-right">{{ __('Grade Point') }}</label>

                                <div class="col-md-6">
                                    <input id="point" type="number" class="form-control @error('point') is-invalid @enderror" name="point" value="{{ $exam_grade->point }}" required autocomplete="point" autofocus>

                                    @error('point')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mark_from" class="col-md-4 col-form-label text-md-right">{{ __('Mark From') }}</label>

                                <div class="col-md-6">
                                    <input id="mark_from" type="number" class="form-control @error('mark_from') is-invalid @enderror" name="mark_from" value="{{ $exam_grade->mark_from }}" required autocomplete="mark_from" autofocus>

                                    @error('mark_from')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mark_upto" class="col-md-4 col-form-label text-md-right">{{ __('Mark Upto') }}</label>

                                <div class="col-md-6">
                                    <input id="mark_upto" type="number" class="form-control @error('mark_upto') is-invalid @enderror" name="mark_upto" value="{{ $exam_grade->mark_upto }}" required autocomplete="mark_upto" autofocus>

                                    @error('mark_upto')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="note" class="col-md-4 col-form-label text-md-right">{{ __('Note') }}</label>

                                <div class="col-md-6">
                                    <textarea id="note" type="text" class="form-control @error('note') is-invalid @enderror" name="note" required autocomplete="note" autofocus>{{ $exam_grade->note }}</textarea>

                                    @error('note')
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
