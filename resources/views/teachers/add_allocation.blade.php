@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Assign New Class Teacher') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('allocation.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <label for="class_teacher" class="col-md-4 col-form-label text-md-right">{{ __('Class Teacher') }}</label>
                                <div class="col-md-6">
                                    <select name="class_teacher" class="form-control @error('class_teacher') is-invalid @enderror" id="class_teacher" required>
                                        <option selected disabled>
                                            Not Selected
                                        </option>
                                        @foreach($teachers as $teacher)
                                            <option
                                                @if($teacher['id'] == old('class_teacher'))
                                                    selected
                                                @endif
                                                value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('class_teacher')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="class" class="col-md-4 col-form-label text-md-right">{{ __('Class') }}</label>
                                <div class="col-md-6">
                                    <select name="class" class="form-control @error('class') is-invalid @enderror" id="class" required>
                                        <option selected disabled>
                                            Not Selected
                                        </option>
                                        @foreach($classes as $class)
                                            <option
                                                @if($class['id'] == old('class'))
                                                selected
                                                @endif
                                                value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('class')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="section" class="col-md-4 col-form-label text-md-right">{{ __('Section') }}</label>
                                <div class="col-md-6">
                                    <select name="section" class="form-control @error('section') is-invalid @enderror" id="section" required>
                                        <option selected disabled>
                                            Select class first
                                        </option>
                                    </select>
                                    @error('section')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <hr>


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

@section('script')
    <script>
        $('#class').on('change', function() {
            var id = this.value;



            //Ajax Load data from ajax
            $.ajax({
                url : "{{ env('APP_URL') }}" + '/class/sections/' + parseInt(id),
                type: "GET",
                dataType: "JSON",
                success: function(data)
                {
                    console.log(data);
                    $("#section").html('');
                    $.each(data, function() {
                        $("#section").append('<option value="' + this.value + '">' + this.name + '</option>')
                    })

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error get data from ajax');
                }
            });
        })
    </script>
@endsection
