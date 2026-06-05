@php $cxCfg = config('checkout_experience', []); @endphp
<section class="card border mb-4 checkout-experience-card" style="border-color:#D4C5A9!important;">
    <div class="card-body">
        <h3 class="h6 luxury-headline">{{ __('Gift presentation & reassurance') }}</h3>
        <p class="small text-muted mb-4">{{ __('Your piece ships in Monte Luca branded packaging with certificate envelopes and plush ring housing where applicable.') }}</p>

        <div class="row g-4 align-items-start checkout-packaging-visual mb-4">
            <div class="col-6 col-md-3">
                <div class="checkout-pack-slot border d-flex align-items-center justify-content-center mx-auto rounded-1 mb-2" style="height:112px;background:linear-gradient(145deg,#1a1512,#30261f);border-color:#cbb79a;">
                    <span class="small text-center text-white-50">{{ __('Rigid<br>outer box') }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="checkout-pack-slot border d-flex align-items-center justify-content-center mx-auto rounded-1 mb-2" style="height:112px;background:linear-gradient(145deg,#2b1811,#482c20);border-color:#cbb79a;">
                    <span class="small text-center text-white-50">{{ __('Ribbon &<br>tissue seal') }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="checkout-pack-slot border d-flex align-items-center justify-content-center mx-auto rounded-1 mb-2" style="height:112px;background:linear-gradient(160deg,#f5f4f2,#dbd5cc);border-color:#beb4a9;">
                    <span class="small text-center">{{ __('Drawer for<br>certs/docs') }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="checkout-pack-slot border d-flex align-items-center justify-content-center mx-auto rounded-1 mb-2" style="height:112px;background:radial-gradient(circle,#0d0d0f,#35363b);border-color:#cbb79a;">
                    <span class="small text-center text-white-50">{{ __('Velvet tray<br>/ ring case') }}</span>
                </div>
            </div>
        </div>

        <hr class="my-4" style="border-color:#ece8e0;">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="filter-label" for="checkout_occasion">{{ __('Occasion') }}</label>
                <select name="checkout_occasion" id="checkout_occasion" class="form-control">
                    <option value="">{{ __('No special framing') }}</option>
                    <option value="proposal">{{ __('Proposal / Engagement') }}</option>
                    <option value="anniversary">{{ __('Anniversary milestone') }}</option>
                    <option value="valentine">{{ __("Valentine's window") }}</option>
                    <option value="birthday">{{ __('Birthday gifting') }}</option>
                    <option value="self">{{ __('Self purchase') }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="filter-label" for="important_date">{{ __('Key date or deadline (optional)') }}</label>
                <input type="date" name="important_date" id="important_date" class="form-control"
                    autocomplete="off">
                <small class="text-muted">{{ __('Adds “arrives before …” reassurance when plausible with our dispatch band.') }}</small>
            </div>
        </div>

        @php
            $fs = $financing_summary ?? [];
            $conciergeInr = (float) ($cxCfg['concierge_suggest_inr'] ?? 500000);
        @endphp
        @if(($fs['principal_inr'] ?? 0) >= $conciergeInr && ($fs['eligible_display'] ?? false))
            <div class="custom-control custom-checkbox mt-3 border p-3" style="border-color:#D4C5A9!important;background:#fcfbf9;">
                <input type="checkbox" name="concierge_requested" id="concierge_requested" value="1" class="custom-control-input">
                <label for="concierge_requested" class="custom-control-label small">
                    <strong>{{ __('Personal shopper / gemologist review') }}</strong> —
                    {{ __('Schedule a concise video QA before fulfilment—we hold dispatch until the review completes for eligible high-value ₹ orders.') }}
                </label>
            </div>
        @else
            <div class="custom-control custom-checkbox mt-3 border p-3" style="border-color:#D4C5A9!important;background:#fcfbf9;">
                <input type="checkbox" name="concierge_requested" id="concierge_requested" value="1" class="custom-control-input">
                <label for="concierge_requested" class="custom-control-label small">
                    <strong>{{ __('Request gemologist call-back') }}</strong> —
                    {{ __('Optional reassurance before payment finalizes—we contact you via the billing email.') }}
                </label>
            </div>
        @endif

        <script type="application/json" id="checkout-landmarks-data">@json($cxCfg['celebratory_landmarks'] ?? [])</script>

        <div class="alert alert-light border mt-4 mb-0 small checkout-delivery-banner" role="region" aria-live="polite"
             data-proc-min="{{ ($cxCfg['processing_business_days']['min'] ?? 2) }}"
             data-proc-max="{{ ($cxCfg['processing_business_days']['max'] ?? 4) }}"
             data-ship-min="{{ ($cxCfg['shipping_business_days']['min'] ?? 2) }}"
             data-ship-max="{{ ($cxCfg['shipping_business_days']['max'] ?? 5) }}">
            <strong class="d-block mb-1">{{ __('Delivery outlook') }}</strong>
            <p class="mb-0" id="checkoutDeliveryOutlook">{{ __('Tell us how you\'re celebrating and we tailor the reassurance line above.') }}</p>
        </div>

        <input type="hidden" name="delivery_estimate_snapshot" id="delivery_estimate_snapshot" value="">
    </div>
</section>

<script>
(function () {
    function onlyDate(d){ return new Date(d.getFullYear(), d.getMonth(), d.getDate()); }
    function addBusinessDays(date, days) {
        var d = onlyDate(new Date(date.getTime()));
        var added = 0;
        while (added < days) {
            d.setDate(d.getDate() + 1);
            var w = d.getDay();
            if (w !== 0 && w !== 6) added++;
        }
        return d;
    }
    function formatRange(a, b) {
        var opts = { month: 'short', day: 'numeric' };
        if (a.getFullYear() !== b.getFullYear()) opts.year = 'numeric';
        var left = a.toLocaleDateString(undefined, opts);
        var right = b.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
        return left + ' – ' + right;
    }
    document.addEventListener('DOMContentLoaded', function () {
        var outlook = document.getElementById('checkoutDeliveryOutlook');
        var hid = document.getElementById('delivery_estimate_snapshot');
        var banner = document.querySelector('.checkout-delivery-banner');
        var occSel = document.getElementById('checkout_occasion');
        var dateIn = document.getElementById('important_date');
        var lmEl = document.getElementById('checkout-landmarks-data');
        if (!outlook || !banner || !occSel || !hid) return;
        var procMin = parseInt(banner.dataset.procMin || '2', 10);
        var procMax = parseInt(banner.dataset.procMax || '4', 10);
        var shipMin = parseInt(banner.dataset.shipMin || '2', 10);
        var shipMax = parseInt(banner.dataset.shipMax || '5', 10);
        var landmarks = {};
        if (lmEl) {
            try { landmarks = JSON.parse(lmEl.textContent || '{}'); } catch (e) {}
        }

        function recompute() {
            var today = onlyDate(new Date());
            var start = addBusinessDays(today, procMin + shipMin);
            var end = addBusinessDays(today, procMax + shipMax);
            var range = formatRange(start, end);
            var baseMsg = '{{ __("Estimated arrival window:") }} ' + '<strong>' + range + '</strong>. {{ __("Bench, hallmarking or remote QA may add a few days—we email if anything slips.") }}';
            var extra = '';
            var occ = occSel.value;
            if (occ === 'valentine' && landmarks.valentine) {
                var vv = landmarks.valentine;
                var v = new Date(today.getFullYear(), vv.month - 1, vv.day);
                if (onlyDate(v) < today) v = new Date(today.getFullYear() + 1, vv.month - 1, vv.day);
                if (end <= onlyDate(v)) {
                    extra = ' {{ __("We are aligning production toward") }} <strong>' + (vv.label || "Valentine's Day") + '</strong>.';
                }
            }
            if (dateIn && dateIn.value && (occ === 'anniversary' || occ === 'birthday')) {
                var target = onlyDate(new Date(dateIn.value + 'T12:00:00'));
                if (target >= today && end <= addBusinessDays(target, -3)) {
                    extra += ' {{ __("Dispatch scheduling targets reassurance ahead of your selected date.") }}';
                }
            }

            outlook.innerHTML = baseMsg + extra;
            hid.value = (baseMsg + extra).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
        }

        occSel.addEventListener('change', recompute);
        if (dateIn) dateIn.addEventListener('change', recompute);
        recompute();
    });
})();
</script>
