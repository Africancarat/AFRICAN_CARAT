@extends('master.front')
@section('title')
    {{ __('Login') }}
@endsection
@section('content')
    <!-- Page Title-->
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a> </li>
                        <li class="separator"></li>
                        <li>{{ __('Verify Email') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->

    <div class="container padding-bottom-3x mb-1">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form class="card auth-form-card" method="post" action="{{ route('user.verify.submit') }}">
                    @csrf
                    <div class="card-body ">
                        <h4 class="margin-bottom-1x text-center">{{ __('Email Verification') }}</h4>

                        <div class="form-group input-group">
                            <input class="form-control" type="text" name="verify"
                                placeholder="{{ __('Enter Verify Code') }}" value="{{ old('verify') }}">
                        </div>
                        @error('verify')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror

                        <div class="text-center">
                            <button class="btn btn-primary margin-bottom-none"
                                type="submit"><span>{{ __('Verify') }}</span></button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Site Footer-->
@endsection
