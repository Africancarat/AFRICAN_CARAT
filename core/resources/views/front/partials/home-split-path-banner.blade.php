@php
    $split = isset($split) && is_array($split) ? $split : [];
    $bgColor = $split['bg_color'] ?? '#F7F5F0';
    $bgImg = trim((string) ($split['bg_image'] ?? ''));
    $fgImg = trim((string) ($split['fg_image'] ?? ''));
    $africanCaratEngagementImage = 'African carat/2.png';
    if (is_file(public_path('storage/images/' . $africanCaratEngagementImage))) {
        $fgImg = $africanCaratEngagementImage;
    }
    $splitStorageImageUrl = function (?string $relativePath): ?string {
        $relativePath = ltrim(trim((string) $relativePath), '/');
        if ($relativePath === '' || ! is_file(public_path('storage/images/' . $relativePath))) {
            return null;
        }

        return url('/core/public/storage/images/' . implode('/', array_map('rawurlencode', explode('/', $relativePath))));
    };
    $kicker = $split['kicker'] ?? '';
    $headline = $split['headline'] ?? '';
    $body = $split['body'] ?? '';
    $wm = $split['watermark_text'] ?? '';
    $b1l = $split['btn1_label'] ?? '';
    $b1u = trim((string) ($split['btn1_url'] ?? ''));
    $b2l = $split['btn2_label'] ?? '';
    $b2u = trim((string) ($split['btn2_url'] ?? ''));
    $or = trim((string) ($split['or_label'] ?? ''));
    if ($or === '') {
        $or = 'OR';
    }
    $fp = $split['foot_prefix'] ?? '';
    $flt = $split['foot_link_text'] ?? '';
    $flu = trim((string) ($split['foot_link_url'] ?? ''));
@endphp
<section class="luxury-split-path-banner" style="@if ($bgImg === '')background-color: {{ e($bgColor) }};@endif">
    @if ($bgImgUrl = $splitStorageImageUrl($bgImg))
        <div class="luxury-split-path-banner__bg-photo" style="background-image:url('{{ $bgImgUrl }}');" aria-hidden="true"></div>
    @endif
    @if ($wm !== '')
        <div class="luxury-split-path-banner__watermark" aria-hidden="true">{{ $wm }}</div>
    @endif
    <div class="container luxury-split-path-banner__inner">
        <div class="row align-items-center luxury-split-path-banner__row">
            <div class="col-lg-6 order-2 order-lg-1 luxury-split-path-banner__copy">
                @if ($kicker !== '')
                    <p class="luxury-split-path-banner__kicker">{{ $kicker }}</p>
                @endif
                @if ($headline !== '')
                    <h2 class="luxury-split-path-banner__headline">{{ $headline }}</h2>
                @endif
                @if ($body !== '')
                    <p class="luxury-split-path-banner__body">{!! nl2br(e($body)) !!}</p>
                @endif

                @if ($b1l !== '' || $b2l !== '')
                    <div class="luxury-split-path-banner__ctas">
                        @if ($b1l !== '')
                            <a href="{{ $b1u !== '' ? $b1u : '#' }}" class="btn btn-luxury luxury-split-path-banner__btn">{{ $b1l }}</a>
                        @endif
                        @if ($b1l !== '' && $b2l !== '')
                            <span class="luxury-split-path-banner__or">{{ $or }}</span>
                        @endif
                        @if ($b2l !== '')
                            <a href="{{ $b2u !== '' ? $b2u : '#' }}" class="btn btn-luxury luxury-split-path-banner__btn">{{ $b2l }}</a>
                        @endif
                    </div>
                @endif

                @if ($fp !== '' || $flt !== '')
                    <p class="luxury-split-path-banner__foot">
                        {{ $fp }}
                        @if ($flt !== '' && $flu !== '')
                            <a href="{{ $flu }}" class="luxury-split-path-banner__foot-link">{{ $flt }}</a>
                        @elseif ($flt !== '')
                            <span>{{ $flt }}</span>
                        @endif
                    </p>
                @endif
            </div>
            <div class="col-lg-6 order-1 order-lg-2 luxury-split-path-banner__visual">
                @if ($fgImgUrl = $splitStorageImageUrl($fgImg))
                    <div class="luxury-split-path-banner__figure">
                        <img src="{{ $fgImgUrl }}"
                            alt="{{ strip_tags($headline !== '' ? $headline : __('Engagement ring')) }}"
                            class="luxury-split-path-banner__fg"
                            loading="lazy">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
