@extends('master.front')
@section('title')
    {{__('Login')}}
@endsection
@section('content')

    <!-- Page Title-->
<div class="page-title">
    <div class="container">
      <div class="row">
          <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                <li class="separator"></li>
                <li>{{__('Register')}}</li>
              </ul>
          </div>
      </div>
    </div>
  </div>
  <!-- Page Content-->

  <div class="container padding-bottom-3x mb-1">
    <div class="row justify-content-center">
            <div class="col-md-6">
              <div class="card register-area auth-form-card">
                  <div class="card-body ">
                      <h4 class="margin-bottom-1x text-center">{{__('Register')}}</h4>
                      <form class="row" action="{{route('user.register.submit')}}" method="POST">
                          @csrf
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="reg-fn">{{__('First Name')}}*</label>
                            <input class="form-control" type="text" name="first_name" placeholder="{{__('First Name')}}" id="reg-fn" value="{{old('first_name')}}">
                          @error('first_name')
                          <p class="text-danger">{{$message}}</p>
                          @endif
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="reg-ln">{{__('Last Name')}}*</label>
                            <input class="form-control" type="text" name="last_name" placeholder="{{__('Last Name')}}" id="reg-ln" value="{{old('last_name')}}">
                            @error('last_name')
                          <p class="text-danger">{{$message}}</p>
                          @endif
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="reg-email">{{__('E-mail Address')}}*</label>
                            <input class="form-control" type="email" name="email" placeholder="{{__('E-mail Address')}}" id="reg-email" value="{{old('email')}}">
                            @error('email')
                            <p class="text-danger">{{$message}}</p>
                            @endif
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="reg-phone">{{__('Phone Number')}}*</label>
                            <input class="form-control" name="phone" type="text" placeholder="{{__('Phone Number')}}" id="reg-phone" value="{{old('phone')}}">
                            @error('phone')
                            <p class="text-danger">{{$message}}</p>
                            @endif
                          </div>
                        </div>
          
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="reg-pass">{{__('Password')}}*</label>
                            <input class="form-control" type="password" name="password" placeholder="{{__('Password')}}" id="reg-pass">
                            @error('password')
                            <p class="text-danger">{{$message}}</p>
                            @endif
                          </div>
          
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="reg-pass-confirm">{{__('Confirm Password')}}*</label>
                            <input class="form-control" type="password" name="password_confirmation" placeholder="{{__('Confirm Password')}}" id="reg-pass-confirm">
                          </div>
                        </div>
                        <input type="text" name="honeypot" id="honeypot" value="" style="display:none;">
                        @if ($setting->recaptcha == 1)
                        <div class="col-lg-12">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                            @if ($errors->has('g-recaptcha-response'))
                            @php
                                $errmsg = $errors->first('g-recaptcha-response');
                            @endphp
                            <p class="text-danger mb-0">{{__("$errmsg")}}</p>
                            @endif
                        </div>
                        @endif

                        <div class="col-lg-12 mb-4">
                          <p>{{ __("Already have an account ?") }} <a class="text-base-color" href="{{ route("user.login") }}">{{ __('Login now') }}</a></p>
                        </div>
          
                        <div class="col-12 text-center">
                          <button class="btn btn-primary margin-bottom-none" type="submit"><span>{{__('Register')}}</span></button>
                        </div>
                      </form>
                  </div>
              </div>
            </div>
          </div>
    </div>
  <!-- Site Footer-->
@endsection
