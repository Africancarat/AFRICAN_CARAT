{{-- Thin top bar: rotates one slide every ~3s (original behaviour) --}}
@php
    $trustStripSlides = [
        [
            'segments' => [
                __('Lifetime Warranty'),
                __('Free worldwide shipping'),
                __('Free returns'),
                __('GIA / independent lab — conflict-free sourcing'),
            ],
        ],
        __('Free engraving on eligible jewels'),
        __('30-day hassle-free returns'),
        ['text' => __('Customer service'), 'url' => route('front.contact')],
    ];
@endphp
<div class="trust-strip trust-strip--top trust-strip--rotating" data-trust-interval="3000">
    <div class="trust-strip__inner trust-strip__inner--rotating">
        <div class="trust-strip__viewport"
             aria-live="polite"
             aria-atomic="true"
             aria-label="{{ __('Store guarantees') }}">
            @foreach ($trustStripSlides as $index => $slide)
                @php $active = $index === 0; @endphp
                @if (is_array($slide) && isset($slide['segments']))
                    <div class="trust-strip__slide trust-strip__slide--cluster{{ $active ? ' is-active' : '' }}" data-trust-slide role="group">
                        @foreach ($slide['segments'] as $seg)
                            <span class="trust-strip__segment">{{ $seg }}</span>
                            @if (! $loop->last)
                                <span class="trust-strip__sep" aria-hidden="true"></span>
                            @endif
                        @endforeach
                    </div>
                @elseif (is_array($slide) && isset($slide['url']))
                    <div class="trust-strip__slide{{ $active ? ' is-active' : '' }}" data-trust-slide role="group">
                        <a href="{{ $slide['url'] }}" class="trust-strip__rotating-link">{{ $slide['text'] }}</a>
                    </div>
                @else
                    <div class="trust-strip__slide{{ $active ? ' is-active' : '' }}" data-trust-slide role="group">
                        <span>{{ $slide }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('.trust-strip--rotating[data-trust-interval]');
        if (!root) return;
        var slides = root.querySelectorAll('[data-trust-slide]');
        if (slides.length < 2) return;

        var ms = parseInt(root.getAttribute('data-trust-interval'), 10);
        if (isNaN(ms) || ms < 1500) ms = 3000;

        var i = 0;
        var prefersReduced =
            typeof window.matchMedia === 'function' &&
            window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReduced) return;

        setInterval(function () {
            slides[i].classList.remove('is-active');
            i = (i + 1) % slides.length;
            slides[i].classList.add('is-active');
        }, ms);
    });
})();
</script>
