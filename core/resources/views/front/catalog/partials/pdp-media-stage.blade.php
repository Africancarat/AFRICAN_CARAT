{{-- iJewel-style media stage: embed viewer, still image, fallback link, empty state --}}
@php
    $pdpViewerMeta = $pdp_viewer_meta ?? ['url' => '', 'has_embed' => false, 'is_image' => false, 'is_fallback_link' => false, 'image_src' => null];
    $pdpMetalImages = $pdp_metal_images ?? [];
    $pdpDefaultMetal = $pdp_default_metal ?? \App\Services\JewelryPdpMediaService::DEFAULT_METAL_KEY;
    $pdpHasViewer = ! empty($pdpViewerMeta['has_embed']);
    $pdpViewerUrl = (string) ($pdpViewerMeta['url'] ?? '');
    $pdpViewerImageSrc = $pdpViewerMeta['image_src'] ?? null;
    $pdpDefaultMetalUrls = \App\Services\JewelryPdpMediaService::sanitizeUrlList($pdpMetalImages[$pdpDefaultMetal] ?? []);
    $pdpThumbPlaceholder = \App\Services\JewelryPdpMediaService::placeholderImageUrl();
    $pdpStageInitialSrc = $pdpViewerImageSrc
        ?? ($pdpDefaultMetalUrls[0] ?? null)
        ?? ($pdp_primary_still ?? null)
        ?? (\App\Services\JewelryPdpMediaService::sanitizeUrlList($pdp_slider_images ?? [])[0] ?? null);
    $pdpHasMetalImages = collect($pdpMetalImages)->flatten()->filter()->isNotEmpty();
    $pdpShowIjGallery = ! empty($pdp_show_media_gallery);
    $pdpStartWithViewer = $pdpHasViewer;
@endphp

<div
    class="pdp-product-media ij-pdp-product-media {{ $pdpShowIjGallery ? 'pdp-product-media--ij-active' : '' }}"
    data-product-media
    data-metal-images='@json($pdpMetalImages)'
    data-default-metal="{{ $pdpDefaultMetal }}"
    data-has-viewer="{{ $pdpHasViewer ? '1' : '0' }}"
    data-product-name="{{ $item->name }}"
    data-viewer-url="{{ $pdpViewerUrl }}"
    data-thumb-placeholder="{{ $pdpThumbPlaceholder }}"
>
    <div class="pdp-media-stage ij-pdp-media-stage" data-media-stage>
        @if ($pdpHasViewer)
            <div class="pdp-media-viewer {{ $pdpStartWithViewer ? '' : 'd-none' }}" data-media-viewer>
                <div class="pdp-media-viewer__frame ratio ratio-16x9">
                    <iframe
                        src="{{ $pdpViewerUrl }}"
                        title="{{ __('3D viewer') }}"
                        class="pdp-media-viewer__iframe"
                        allow="fullscreen; autoplay"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
                <p class="pdp-media-viewer__caption small text-muted text-center mt-2 mb-0">
                    {{ __('3D viewer') }}
                    <a href="{{ $pdpViewerUrl }}" target="_blank" rel="noreferrer" class="ms-1">{{ __('Open in new tab') }}</a>
                </p>
            </div>
        @elseif ($pdpViewerMeta['is_fallback_link'] ?? false)
            <div class="pdp-media-fallback text-center p-4 {{ $pdpStageInitialSrc ? 'd-none' : '' }}" data-media-fallback>
                <p class="small text-muted mb-2">{{ __('Viewer link found (not an image).') }}</p>
                <a href="{{ $pdpViewerUrl }}" target="_blank" rel="noreferrer" class="btn btn-outline-secondary btn-sm rounded-pill">
                    {{ __('Open viewer') }}
                </a>
            </div>
        @endif

        @if ($pdpStageInitialSrc)
            <div class="pdp-media-stage__img-wrap {{ $pdpStartWithViewer ? 'd-none' : '' }}" data-media-image-wrap data-lux-zoom>
                <img
                    data-media-image
                    src="{{ $pdpStageInitialSrc }}"
                    alt=""
                    class="pdp-media-stage__img"
                    loading="eager"
                    decoding="async"
                    onerror="this.onerror=null;this.src='{{ $pdpThumbPlaceholder }}';"
                >
            </div>
        @else
            <img data-media-image alt="" class="pdp-media-stage__img d-none" hidden>
        @endif

        @if (! $pdpHasViewer && ! $pdpStageInitialSrc && $pdpViewerUrl === '' && ! $pdpHasMetalImages)
            <div class="pdp-media-empty" data-metal-empty>{{ __('No image available') }}</div>
        @else
            <div class="pdp-media-empty d-none" data-metal-empty>{{ __('No image available') }}</div>
        @endif
    </div>

    @if ($pdpShowIjGallery)
        @include('front.catalog.partials.pdp-metal-gallery', [
            'item' => $item,
            'pdp_metal_images' => $pdpMetalImages,
            'pdp_default_metal' => $pdpDefaultMetal,
            'pdp_has_viewer' => $pdpHasViewer,
            'pdp_default_metal_urls' => $pdpDefaultMetalUrls,
        ])
    @endif
</div>
