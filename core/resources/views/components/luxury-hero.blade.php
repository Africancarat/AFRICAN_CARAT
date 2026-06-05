@php
    $heroImage = $heroImage ?? asset('images/hero-diamond.jpg');
    /** @var string|null $heroVideo Absolute or relative URL to .mp4 / .webm */
    $heroVideo = $heroVideo ?? null;
    $isVideoHero = $heroVideo && preg_match('/\.(mp4|webm)(\?|#|$)/i', (string) $heroVideo);
    $title = $title ?? __('Find the Diamond That Defines the Moment');
    $eyebrow = $eyebrow ?? null;
    $primaryLink = $primaryLink ?? route('diamonds.index');
    $primaryLabel = $primaryLabel ?? __('Explore diamonds');
@endphp

<section class="luxury-hero">
    @if ($isVideoHero)
        @php $videoSrc = preg_match('#^https?://#', (string) $heroVideo) ? $heroVideo : url($heroVideo); @endphp
        <video class="luxury-hero__video" autoplay muted loop playsinline webkit-playsinline
               poster="{{ $heroImage }}" aria-hidden="true">
            <source src="{{ $videoSrc }}" type="{{ str_contains(strtolower($videoSrc), 'webm') ? 'video/webm' : 'video/mp4' }}">
        </video>
        <div class="luxury-hero__overlay"></div>
    @else
        <div class="luxury-hero__bg" style="background-image:url('{{ $heroImage }}')"></div>
        <div class="luxury-hero__overlay"></div>
    @endif
    <div class="luxury-hero__content">
        @if ($eyebrow)
            <p class="luxury-hero__eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="luxury-hero__title">{!! $title !!}</h1>
        <div class="luxury-hero__ctas">
            <a href="{{ $primaryLink }}" class="btn-luxury">{{ $primaryLabel }}</a>
        </div>
    </div>
</section>
