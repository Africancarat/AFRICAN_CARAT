@extends('master.front')

@section('title')
    {{__('Page')}}
@endsection

@section('content')
    <!-- Page Title-->
<div class="page-title">
  <div class="container">
    <div class="row">
        <div class="col-lg-12">
            <ul class="breadcrumbs">
                <li><a href="{{route('front.index')}}">{{__('Home')}}</a> </li>
                <li class="separator">&nbsp;</li>
                <li>{{$page->title}}</li>
              </ul>
        </div>
    </div>
  </div>
</div>
<!-- Page Content-->
<div class="">
    <div class="container other-page-data">
        <!-- Categories-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5">
                    <div class="card-body px-3 py-5">
                        <div class="d-page-content">
                            <h4 class="d-block text-center"><b>{{$page->title}}</b></h4>
                            {!! $page->details !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
</div>

@endsection
