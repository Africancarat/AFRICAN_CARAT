@if (! empty($financing_summary['eligible_display']))
    <section class="card widget widget-featured-posts mb-4 p-4 checkout-emi-card" style="border-color:#d4c5a9;">
        <h3 class="widget-title h6">{{ __('EMI & BNPL — India') }}</h3>
        @if (\App\Helpers\FinancingHelper::isRazorpayEnabled())
            <p class="small text-muted">{{ __('Eligible bank EMI surfaces inside Razorpay after you tap Pay. Figures below follow standard reducing balance math for planning only.') }}</p>
        @else
            <p class="small text-muted">{{ __('Enable Razorpay in Payment Settings with INR settlements to unlock live EMI issuers.') }}</p>
        @endif
        <ul class="list-unstyled small mb-0">
            @foreach (($financing_summary['plans'] ?? []) as $plan)
                <li class="py-1 border-bottom" style="border-color:#f0eae0!important;">
                    <strong>{{ (int) $plan['months'] }} {{ __('mo') }}</strong>
                    —
                    ₹{{ number_format((float) $plan['emi_inr_rounded'], 0, '.', ',') }}{{ __(' / mo indicative') }}
                    <span class="text-muted">({{ __('~') }}{{ number_format((float) $plan['apr_percent'], 2) }}% {{ __('APR') }})</span>
                </li>
            @endforeach
        </ul>
        <p class="small mb-0 mt-2">{{ __('International shoppers may receive regional BNPL wallets from Stripe or PayPal when those gateways publish offers.') }}</p>
    </section>
@endif
