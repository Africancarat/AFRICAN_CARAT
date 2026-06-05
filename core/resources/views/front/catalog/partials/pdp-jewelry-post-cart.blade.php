

@if (($show_emi_estimate ?? false) && (float) $item->discount_price > 0)
    @php
        $fp = ! empty($financing_pdp) ? $financing_pdp : \App\Helpers\FinancingHelper::plansForPrincipalInr((float) $item->discount_price);
        $minEmiInr = (float) config('financing.min_order_inr', 2500);
    @endphp
    @if (! empty($fp['eligible_display']))
        <div class="mb-3 pdp-emi-card border p-3 pdp-emi-card-border">
            <div class="filter-label">{{ __('Estimated EMI') }}</div>
            @if (! empty($fp['razorpay_live_emi_note']))
                <p class="small text-muted mb-2">{{ $fp['razorpay_live_emi_note'] }}</p>
            @endif
            <ul class="list-unstyled small mb-0">
                @foreach (($fp['plans'] ?? []) as $plan)
                    <li class="py-1 border-bottom" style="border-color:#f0eae0!important;">
                        <strong>{{ (int) $plan['months'] }} {{ __('mo') }}</strong>
                        —
                        ₹{{ number_format((float) $plan['emi_inr_rounded'], 0, '.', ',') }}{{ __(' / mo indicative') }}
                        <span class="text-muted">({{ __('~') }}{{ number_format((float) $plan['apr_percent'], 2) }}%
                            {{ __('APR') }})</span>
                    </li>
                @endforeach
            </ul>
            <p class="small text-muted mb-0 mt-2">
                {{ __('Final tenure and APR are chosen with your issuing bank inside Razorpay at payment.') }}</p>
        </div>
    @else
        <div class="mb-3 pdp-emi-card border p-3 pdp-emi-card-border">
            <div class="filter-label">{{ __('Installments') }}</div>
            <p class="small text-muted mb-0">
                @if (($fp['currency'] ?? '') !== 'INR')
                    {{ __('Razorpay bank EMI applies when your session currency is ₹. Switch currency to preview indicative instalments.') }}
                @elseif (! \App\Helpers\FinancingHelper::isRazorpayEnabled())
                    {{ __('Enable Razorpay in payment settings for live EMI choices at checkout.') }}
                @elseif ((float) ($fp['principal_inr'] ?? 0) < $minEmiInr)
                    {{ __('Indicative EMI from about ₹') }}{{ number_format($minEmiInr, 0, '.', ',') }}{{ __(' cart value.') }}
                @else
                    {{ __('Complete checkout in ₹ to see issuer-specific EMI in the Razorpay window.') }}
                @endif
            </p>
        </div>
    @endif
@endif

@if ($show_drop_hint ?? false)
    <div class="mb-3">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
            data-bs-target="#dropHintModal">
            {{ __('Drop a hint') }}
        </button>
        <span class="small text-muted d-block mt-1">{{ __('Email this product to someone special.') }}</span>
    </div>
@endif

@if ($item->diamondAttribute)
    <div class="mb-3 diamond-live-inspect">
        <button type="button" class="btn btn-outline-dark btn-sm"
            onclick="requestDiamondInspection({{ $item->id }})">
            {{ __('Request live gemologist tour') }}
        </button>
        <span class="small text-muted d-block mt-1">{{ __('We will follow up to schedule a guided review (Retell AI + Twilio wiring optional).') }}</span>
    </div>
@endif
