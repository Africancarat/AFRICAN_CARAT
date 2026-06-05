<aside class="sidebar">
    <div class="padding-top-2x hidden-lg-up"></div>
    <!-- Items in Cart Widget-->


    <section class="card widget widget-featured-posts widget-order-summary p-4">
        <h3 class="widget-title">{{ __('Order Summary') }}</h3>
        @php
            $free_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
        @endphp

        @if ($free_shipping)
            @if ($free_shipping->minimum_price >= $cart_total)
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


    <section class="card widget widget-featured-posts widget-featured-products p-4">
        <h3 class="widget-title">{{ __('Items In Your Cart') }}</h3>
        @foreach ($cart as $key => $item)
            <div class="entry">
                <div class="entry-thumb"><a href="{{ route('front.product', $item['slug']) }}"><img
                            src="{{ \App\Helpers\ImageHelper::storageImageUrl($item['photo'] ?? null) }}" alt="Product"></a>
                </div>
                <div class="entry-content">
                    <h4 class="entry-title"><a href="{{ route('front.product', $item['slug']) }}">
                            {{ Str::limit($item['name'], 40) }}

                        </a></h4>
                    <span class="entry-meta">{{ $item['qty'] }} x
                        @php
                            $totalAttributePrice = 0;
                            foreach ($item['attribute']['option_price'] as $option_price) {
                                $totalAttributePrice += $option_price;
                            }
                            $price = $item['main_price'] + $totalAttributePrice;
                        @endphp
                        {{ PriceHelper::setCurrencyPrice($price) }}.</span>

                    @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                        <div class="entry-meta">
                            <span class="entry-meta d-inline">{{ $item['attribute']['names'][$optionkey] }}:</span>
                            <span class="entry-meta d-inline"><b>{{ $option_name }}</b></span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

</aside>
