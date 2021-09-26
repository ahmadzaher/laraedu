@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card-box bg-blue">
                                <div class="inner">
                                    <h3> {{ $number_of_students }} </h3>
                                    <p> Students </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                </div>
                                <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="card-box bg-green">
                                <div class="inner">
                                    <h3> {{ $number_of_teachers }} </h3>
                                    <p> Teachers </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </div>
                                <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <div class="card-box bg-orange">
                                <div class="inner">
                                    <h3> {{ $number_of_staffs }} </h3>
                                    <p> Staffs </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users" aria-hidden="true"></i>
                                </div>
                                <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

{{--                        <div class="col-lg-3 col-sm-6">--}}
{{--                            <div class="card-box bg-green">--}}
{{--                                <div class="inner">--}}
{{--                                    <h3> ₹185358 </h3>--}}
{{--                                    <p> Today’s Collection </p>--}}
{{--                                </div>--}}
{{--                                <div class="icon">--}}
{{--                                    <i class="fa fa-money" aria-hidden="true"></i>--}}
{{--                                </div>--}}
{{--                                <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="col-lg-3 col-sm-6">
                            <div class="card-box bg-red">
                                <div class="inner">
                                    <h3> {{ $number_of_subjects }} </h3>
                                    <p> Subjects </p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-book"></i>
                                </div>
                                <a href="#" class="card-box-footer">View More <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
