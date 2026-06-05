@extends('master.front')

@section('title', 'Trust, Hallmarking & Compliance')

@section('meta')
    <meta name="description" content="{{ __('Monte Luca / African Carat — hallmark references, laboratories, destinations, PCI-DSS security & duty transparency.') }}">
@endsection

@section('content')
<div class="page-title">
    <div class="container">
        <ul class="breadcrumbs">
            <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
            <li class="separator"></li>
            <li><a href="{{ route('education.index') }}">{{ __('Education') }}</a></li>
            <li class="separator"></li>
            <li>{{ __('Compliance') }}</li>
        </ul>
    </div>
</div>

<div class="container py-5 mb-5 compliance-page">
    <h1 class="luxury-headline mb-4">{{ __('International hallmarking & compliance signals') }}</h1>
    <p class="text-muted">{{ __('Final legal copy must be validated by Monte Luca Ltd / African Carat counsel. Replace bracketed notes with assay license numbers actually on file.') }}</p>

    <div class="row g-4 mt-3">
        <div class="col-lg-6">
            <div class="border p-4 h-100" style="border-color:#D4C5A9!important;">
                <h2 class="h5 luxury-headline">{{ __('India — BIS hallmark') }}</h2>
                <p class="small">{{ __('Jewelry dispatched from or hallmarked within India aligns with Bureau of Indian Standards stamping where applicable gold content requires disclosure. SKU detail pages denote metal fineness.') }}</p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="border p-4 h-100" style="border-color:#D4C5A9!important;">
                <h2 class="h5 luxury-headline">{{ __('UAE — DMCC / Dubai Precious Metals') }}</h2>
                <p class="small">{{ __('Consignments routed via Dubai cite DMCC-aligned documentation for bullion-derived metals; vendor invoices remain available upon customs request.') }}</p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="border p-4 h-100" style="border-color:#D4C5A9!important;">
                <h2 class="h5 luxury-headline">{{ __('United Kingdom — Assay alignment') }}</h2>
                <p class="small">{{ __('Monte Luca references UK hallmark requirements for domestic retail placements; hallmark artwork can be surfaced on PDP when selling UK-assayed pieces.') }}</p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="border p-4 h-100" style="border-color:#D4C5A9!important;">
                <h2 class="h5 luxury-headline">{{ __('Import duties') }}</h2>
                <p class="small">{{ __('Duties, VAT/GST/HST calculations at checkout approximate live carrier feeds; customers remain importer of record. Contact care@ for HS-code references ahead of freight release.') }}</p>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="border p-4" style="border-color:#D4C5A9!important;">
                <h2 class="h5 luxury-headline">{{ __('Payments — PCI-DSS') }}</h2>
                <p class="small mb-0">{{ __('Card data is transmitted through PCI DSS–certified gateways (Razorpay / Stripe / PayPal as enabled). OmniMart cores never persist full PAN/CVV on application servers.') }}</p>
            </div>
        </div>
    </div>

    <p class="small text-muted mt-4 mb-0">
        {{ __('Diamond listings reference independent laboratories (GIA, IGI, HRD…) with verification deeplinks. See ') }}
        <a href="{{ route('education.guides.show', ['slug' => 'certification-explained']) }}">{{ __('Certificates explained') }}</a>.
    </p>
</div>
@endsection
