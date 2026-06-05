{{-- Trusted by clients — storage fallbacks via HomePageImageHelper --}}
@php
    use App\Helpers\HomePageImageHelper;
@endphp
<section class="custom-trust">
    <div class="container">
        <h2 class="trust-heading">{{ __('Trusted by private clients across global markets.') }}</h2>
        <div class="trust-grid">
            <div class="trust-item">
                <img class="trust-photo trust-photo--igi" src="{{ HomePageImageHelper::trustUrl('igi') }}" alt="{{ __('IGI Certified') }}" loading="lazy">
                <p>{{ __('IGI Certified') }}</p>
            </div>
            <div class="trust-item">
                <img class="trust-photo trust-photo--checkout" src="{{ HomePageImageHelper::trustUrl('checkout') }}" alt="{{ __('Secure Checkout') }}" loading="lazy">
                <p>{{ __('Secure Checkout') }}</p>
            </div>
            <div class="trust-item">
                <img class="trust-photo trust-photo--large" src="{{ HomePageImageHelper::trustUrl('delivery') }}" alt="{{ __('Global Delivery') }}" loading="lazy">
                <p>{{ __('Global Delivery') }}</p>
            </div>
            <div class="trust-item">
                <img class="trust-photo trust-photo--large" src="{{ HomePageImageHelper::trustUrl('consultation') }}" alt="{{ __('Private Consultation Available') }}" loading="lazy">
                <p>{{ __('Private Consultation Available') }}</p>
            </div>
        </div>
    </div>
</section>
