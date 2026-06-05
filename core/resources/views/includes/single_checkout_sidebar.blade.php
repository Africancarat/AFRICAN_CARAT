<aside class="sidebar">
    <div class="padding-top-2x hidden-lg-up"></div>
    <!-- Items in Cart Widget-->

    <section class="card widget widget-featured-posts widget-order-summary p-4">
        <h3 class="widget-title">{{ __('Order Summary') }}</h3>
        @php
            $free_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
        @endphp

        @if ($free_shipping && $free_shipping->minimum_price !== null)
            @if ($free_shipping->minimum_price > $cart_total)
                <p class="free-shippin-aa"><em>{{ __('Free Shipping After Order') }}
                        {{ PriceHelper::setCurrencyPrice($free_shipping->minimum_price) }}</em></p>
            @endif
        @endif

        <table class="table">
            <tr>
                <td>{{ __('Cart subtotal') }}:</td>
                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
            </tr>

            @if ($tax != 0)
                <tr>
                    <td>{{ __('Estimated tax') }}:</td>
                    <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($tax) }}</td>
                </tr>
            @endif

            @if (DB::table('states')->count() > 0)
                <tr class="{{ Auth::check() && Auth::user()->state_id ? '' : 'd-none' }} set__state_price_tr">
                    <td>{{ __('State tax') }}:</td>
                    <td class="text-gray-dark set__state_price">
                        {{ PriceHelper::setCurrencyPrice(Auth::check() && Auth::user()->state_id ? ($cart_total * Auth::user()->state->price) / 100 : 0) }}
                    </td>
                </tr>
            @endif

            @if ($discount)
                <tr>
                    <td>{{ __('Coupon discount') }}:</td>
                    <td class="text-danger">-
                        {{ PriceHelper::setCurrencyPrice($discount ? $discount['discount'] : 0) }}</td>
                </tr>
            @endif

            @if ($shipping)
                <tr class="d-none set__shipping_price_tr">
                    <td>{{ __('Shipping') }}:</td>
                    <td class="text-gray-dark set__shipping_price">
                        {{ PriceHelper::setCurrencyPrice($shipping ? $shipping->price : 0) }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-lg text-primary">{{ __('Order total') }}</td>
                <td class="text-lg text-primary grand_total_set">{{ PriceHelper::setCurrencyPrice($grand_total) }}
                </td>
            </tr>
        </table>
    </section>

    @if (PriceHelper::CheckDigital() == true)
    <section class="card widget widget-featured-posts widget-order-summary p-4">
        <h3 class="widget-title">{{ __('Shipping Options') }}</h3>
        <div class="row">
            <div class="col-sm-12 mb-3">
                @if (PriceHelper::CheckDigital() == true)
                    @php
                        $free_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
                    @endphp

                    <select name="shipping_id" class="form-control" id="shipping_id_select" required>
                        <option value="" selected disabled>{{ __('Select Shipping Method') }}*</option>
                        @foreach (DB::table('shipping_services')->whereStatus(1)->get() as $shipping)
                            @if ($shipping->id == 1 && isset($free_shipping) && $free_shipping->minimum_price !== null && $free_shipping->minimum_price <= $cart_total)
                                <option value="{{ $shipping->id }}" data-href="{{ route('front.shipping.setup') }}">
                                    {{ $shipping->title }}
                                </option>
                            @else
                                @if ($shipping->id != 1 && (!isset($free_shipping) || $free_shipping->minimum_price === null || $free_shipping->minimum_price > $cart_total))
                                    <option value="{{ $shipping->id }}"
                                        data-href="{{ route('front.shipping.setup') }}">{{ $shipping->title }}
                                        ({{ PriceHelper::setCurrencyPrice($shipping->price) }})
                                    </option>
                                @endif
                            @endif
                        @endforeach
                    </select>
                    @error('shipping_id')
                        <p class="text-danger shipping_message">{{ $message }}</p>
                    @enderror
                @endif
            </div>
            <div class="col-sm-12 mb-3">
                @if (PriceHelper::CheckDigital() == true)
                    @if (DB::table('states')->whereStatus(1)->count() > 0)
                        <select name="state_id" class="form-control" id="state_id_select" required>
                            <option value="" selected disabled>{{ __('Select Shipping State') }}*</option>
                            @foreach (DB::table('states')->whereStatus(1)->get() as $state)
                                <option value="{{ $state->id }}" data-href="{{ route('front.state.setup') }}"
                                    {{ Auth::check() && Auth::user()->state_id == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                    @if ($state->type == 'fixed')
                                        ({{ PriceHelper::setCurrencyPrice($state->price) }})
                                    @else
                                        ({{ $state->price }}%)
                                    @endif

                                </option>
                            @endforeach
                        </select>
                        @error('state_id')
                            <p class="text-danger state_message">{{ $message }}</p>
                        @enderror
                    @endif
                @endif
            </div>
        </div>

    </section>
    @endif



    <!-- Order Summary Widget-->
    <section class="card widget  widget-order-summary p-4 mb-0">
        <h3 class="widget-title">{{ __('Pay now') }}</h3>
        <div class="row">
            <div class="col-sm-12">
                @php
                    $gateways = DB::table('payment_settings')->whereStatus(1)->get();
                @endphp
                <select class="form-control payment_gateway" required>
                    <option value="" selected disabled>{{ __('Select a payment method') }}</option>
                    @foreach ($gateways as $gateway)
                        @if (PriceHelper::CheckDigitalPaymentGateway())
                            @if ($gateway->unique_keyword != 'cod')
                                <option value="{{ $gateway->unique_keyword }}">{{ $gateway->name }}</option>
                            @endif
                        @else
                            <option value="{{ $gateway->unique_keyword }}">{{ $gateway->name }}</option>
                        @endif
                    @endforeach
                </select>

                @if ($setting->is_privacy_trams == 1)
                    <div class="form-group mt-4">
                        <div class="custom-control d-flex custom-checkbox">
                            <input class="custom-control-input me-2" type="checkbox" id="trams__condition_single"
                                value="">
                            <label class="custom-control-label flex-1" for="trams__condition">
                                {{ __('This site is protected by reCAPTCHA and the') }} <a href="{{ $setting->policy_link }}" target="_blank">{{ __('Privacy Policy') }}</a> {{ __('and') }} <a
                                    href="{{ $setting->terms_link }}" target="_blank">{{ __('Terms of Service') }}</a>
                                {{ __('apply.') }}</label>
                        </div>
                    </div>
                @endif
                @if ($setting->is_privacy_trams == 1)
                    <button id="single_checkout_payment" disabled="true"
                        class="btn btn-primary mt-4 single_checkout_payment" type="submit"><span>{{ __('Pay now') }}</span></button>
                @endif
                @if ($setting->is_privacy_trams == 0)
                    <button id="single_checkout_payment"
                        class="btn btn-primary mt-4 single_checkout_payment" type="submit"><span>{{ __('Pay now') }}</span></button>
                @endif
            </div>

        </div>
    </section>

</aside>

@section('script')
    <script>
        // Show the modal on #single_checkout_payment change
        $(document).on("click", "#single_checkout_payment", function() {
            let keyword = $('.payment_gateway').val();
            let modalElement = document.getElementById(keyword);

            if (modalElement) {
                // Open the modal using Bootstrap 5's API
                let modal = new bootstrap.Modal(modalElement);
                modal.show();

                // Get all input fields from the #checkoutBilling form
                let allinput = $("#checkoutBilling input");

                // Clear the modal form before appending new hidden inputs
                $(modalElement).find('form').html(); // Clear modal form content

                // Loop through each input and append a hidden input in the modal form
                allinput.each(function() {
                    // Create a new hidden input field with the same name and value
                    let hiddenInput = $('<input>')
                        .attr('type', 'hidden') // Set the input type to hidden
                        .attr('name', $(this).attr('name')) // Use the same name attribute
                        .val($(this).val()); // Set the value of the hidden input

                    // Append the hidden input to the modal form
                    $(modalElement).find('form').append(hiddenInput);
                });
            }
        });

        // Handle the "Terms and Conditions" checkbox click
        $(document).on("click", "#trams__condition_single", function() {
            if ($("#trams__condition_single").is(':checked')) {
                console.log("check");
                // Enable the dropdown by assigning the ID and removing the disabled attribute
                $('.single_checkout_payment').attr('id', "single_checkout_payment");
                $('.single_checkout_payment').attr('disabled', false);
            } else {
                // Remove the ID and disable the dropdown when unchecked
                $('.single_checkout_payment').removeAttr('id');
                $('.single_checkout_payment').attr('disabled', true);
            }
        });
    </script>
@endsection
