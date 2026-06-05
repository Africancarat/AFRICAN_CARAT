@extends('master.back')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Storage') }}</b></h3>
                </div>
        </div>
    </div>

	<!-- Form -->
	<div class="row">

		<div class="col-xl-12 col-lg-12 col-md-12">

			<div class="card o-hidden border-0 shadow-lg">
				<div class="card-body ">
					<!-- Nested Row within Card Body -->
					<div class="row">
						<div class="col-xl-12">
                            <form class="admin-form" action="{{ route('back.setting.storage.link') }}" method="POST"
                                enctype="multipart/form-data"> @csrf

                                @include('alerts.alerts')

                                    <h3 class="mb-3">
                                        <b>
                                            {{ __('If you are facing issue with view image, Click this button to link your storage to the website.') }}
                                        </b>
                                    </h3>
                                    <div class="form-group p-0">
                                        <button type="submit" class="btn btn-secondary">{{ __('Storage Link') }}</button>
                                    </div>

                            </form>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>

@endsection
