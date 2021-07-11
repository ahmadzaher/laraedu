@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Edit Role') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('role.update', $role->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $role->name }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="slug" class="col-md-4 col-form-label text-md-right">{{ __('Slug') }}</label>

                                <div class="col-md-6">
                                    <input id="slug" type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ $role->slug }}" required autocomplete="slug" autofocus>

                                    @error('slug')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <hr />
                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Permissions:') }}</label>
                                <div class="d-flex justify-content-center">
                                    <div class="form-group row h-100 mx-auto text-center">
                                        <?php $name = ''; ?>

                                        @foreach($permissions as $permission)

                                            <?php
                                            $new = false;
                                            $strArray = explode(' ', $permission['name']);
                                            $permissionName = $strArray[1];

                                            if($permissionName != $name){
                                                if($name != '')
                                                    echo '</div>';
                                                echo '<div class="col-md-4 ">';
                                                echo '<br>'.$permissionName . ':<br><br>';
                                                $new = true;
                                            }
                                            $name = $permissionName;
                                            ?>
                                            <div class="form-check">
                                                <input

                                                    @if(in_array($permission['id'], $role_permissions))
                                                    checked
                                                    @endif
                                                    class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission['id'] }}" id="permission{{ $permission['id'] }}">
                                                <label class="form-check-label" for="permission{{ $permission['id'] }}">
                                                    {{ $permission['name'] }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div></div>
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
