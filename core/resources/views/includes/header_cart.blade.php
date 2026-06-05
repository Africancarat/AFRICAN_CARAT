@php
    $grandSubtotal = 0;
    $qty = 0;
    $option_price = 0;
@endphp
@if (Session::has('cart'))
    @foreach (Session::get('cart') as $key => $cart)
        @php
            $grandSubtotal = ($cart['main_price'] + $grandSubtotal + $cart['attribute_price']) * $cart['qty'];
        @endphp
        <div class="entry">
            <div class="entry-thumb"><a href="{{ route('front.product', $cart['slug']) }}"><img
                        src="{{ \App\Helpers\ImageHelper::storageImageUrl($cart['photo'] ?? null) }}" alt="Product"></a></div>
            <div class="entry-content">
                <h4 class="entry-title"><a href="{{ route('front.product', $cart['slug']) }}">
                        {{ Str::limit($cart['name'], 29) }}
                    </a></h4>
                <span class="entry-meta">{{ $cart['qty'] }} x
                    @php
                        $totalAttributePrice = 0;
                        foreach ($cart['attribute']['option_price'] as $option_price) {
                            $totalAttributePrice += $option_price;
                        }
                        $price = $cart['main_price'] + $totalAttributePrice;
                    @endphp
                    {{ PriceHelper::setCurrencyPrice($price) }}
                  </span>
                @foreach ($cart['attribute']['option_name'] as $optionkey => $option_name)
                    <span class="att"><em>{{ $cart['attribute']['names'][$optionkey] }}:</em> {{ $option_name }}</span>
                @endforeach
                @if (! empty($cart['engraving']))
                    <span class="att d-block"><em>{{ __('Engraving') }}:</em> {{ $cart['engraving'] }}</span>
                @endif

            </div>
            <div class="entry-delete"><a href="{{ route('front.cart.destroy', $key) }}"><i class="icon-x"></i></a></div>
        </div>
    @endforeach
    <div class="text-right">
        <p class="text-gray-dark py-2 mb-0"><span class="text-muted">{{ __('Subtotal') }}:</span>
            {{ PriceHelper::setCurrencyPrice($grandSubtotal) }}</p>
    </div>
    <div class="d-flex justify-content-between">
        <div class="w-50 d-block"><a class="btn btn-luxury btn-sm mb-0"
                href="{{ route('front.cart') }}"><span>{{ __('Cart') }}</span></a></div>
        <div class="w-50 d-block text-end"><a class="btn btn-luxury btn-sm mb-0"
                href="{{ route('front.checkout.billing') }}"><span>{{ __('Checkout') }}</span></a></div>
    </div>
@else
    {{ __('Cart empty') }}
@endif
