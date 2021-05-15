@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card" style="width:24rem;margin:auto;">
            <div class="card-body">
                <form action="{{route('store.plan')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="plan name">Plan Name:</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="cost">Cost:</label>

                        <input id="cost" type="number" class="form-control @error('cost') is-invalid @enderror" name="cost" value="{{ old('cost') }}" required autocomplete="cost" autofocus>

                        @error('cost')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="cost">Plan Description:</label>
                        <input type="text" class="form-control" name="description" placeholder="Enter Description">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
