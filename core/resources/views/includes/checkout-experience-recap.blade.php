@php $b = Session::get('billing_address', []); @endphp
@if (! empty($b['checkout_occasion']) || ! empty($b['important_date']) || ! empty($b['delivery_estimate_snapshot']) || ! empty($b['concierge_requested']))
    <div class="checkout-experience-recap mb-4 p-4 border rounded-1" style="border-color:#D4C5A9!important;background:#fdfbf7;">
        <h3 class="h6 luxury-headline mb-3">{{ __('Your gifting context') }}</h3>
        <ul class="list-unstyled small mb-0">
            @if (! empty($b['checkout_occasion']))
                <li><strong>{{ __('Occasion') }}:</strong> {{ ucwords(str_replace('_', ' ', (string) $b['checkout_occasion'])) }}</li>
            @endif
            @if (! empty($b['important_date']))
                <li><strong>{{ __('Important date') }}:</strong> {{ $b['important_date'] }}</li>
            @endif
            @if (! empty($b['delivery_estimate_snapshot']))
                <li><strong>{{ __('Delivery reassurance') }}:</strong> {{ $b['delivery_estimate_snapshot'] }}</li>
            @endif
            @if (! empty($b['concierge_requested']))
                <li class="text-success"><strong>{{ __('Concierge QA requested') }}</strong> — {{ __('our team schedules a confirmation before fulfilment.') }}</li>
            @endif
        </ul>
    </div>
@endif
