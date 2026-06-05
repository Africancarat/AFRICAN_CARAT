@extends('master.back')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h3 class="mb-0 bc-title"><b>{{ __('Edit Order ID') }}</b></h3>
                    <a class="btn btn-primary  btn-sm" href="{{ route('back.order.index') }}"><i
                            class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="row">

            <div class="col-xl-12 col-lg-12 col-md-12">

                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="p-5">
                                    <form class="admin-form" action="{{ route('back.order.update', $order->id) }}"
                                        method="POST" enctype="multipart/form-data">

                                        @csrf
                                        @include('alerts.alerts')

                                        <div class="form-group">
                                            <label for="transaction_number">{{ __('Order ID') }} *</label>
                                            <input type="text" name="transaction_number" class="form-control item-name"
                                                id="transaction_number" placeholder="{{ __('Enter Order ID') }}"
                                                value="{{ $order->transaction_number }}" required>
                                        </div>

                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-secondary ">{{ __('Submit') }}</button>
                                        </div>
                                        <div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
