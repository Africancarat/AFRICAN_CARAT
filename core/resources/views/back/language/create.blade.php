@extends('master.back')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h3 class=" mb-0 bc-title"><b>{{ __('Language Create') }}</b> </h3>
                    <a class="btn btn-primary btn-sm" href="{{ route('back.language.index') }}"><i
                            class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                </div>
            </div>
        </div>

        <!-- Content Row -->

        <div class="row">

            <div class="col-xl-12 col-lg-12">


                <form class="geniusform" action="{{ route('back.language.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @include('alerts.alerts')
                    @csrf
                
                    <input type="hidden" name="language" value="{{ $data->language }}">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{ __('Set Language Name') }} </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Language Name *</label>
                                <input type="text" class="form-control" name="language" value="{{ $data->language }}"
                                    placeholder="{{ __('Enter Language Name') }}">

                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-secondary">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
