@extends('layouts.app')

@section('content')
<div class="container">
    <div class="bg-white ">
        <div class="h2 p-3">
            @role('developer')

            Hello developer

            @endrole
        </div>
    </div>
</div>

@endsection
