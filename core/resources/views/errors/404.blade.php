@extends('master.front')

@section('title')
    {{ __('404 | Not Found') }}
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
                        <li>{{ __('404 | Not Found') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="fourzerofour py-5 my-5">
        <div class="container py-5 mt-5 mb-3">
          <div class="row">
            <div class="col-lg-12">
              <div class="content text-center">
                <h3 class="heading">
                  <strong>{{ __('404 | Not Found') }}</strong>
                </h3>
                <p class="text">
                  {{ __('The resource request could not be found on this server !') }}
                </p>
                <a href="{{ route('front.index') }}">
                  <button class="btn btn-primary">
                    <span>{{ __('Back Home') }}</span>
                  </button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
@endsection



