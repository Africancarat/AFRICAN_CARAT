@php
    $pdpMetalImages = $pdp_metal_images ?? [];
    $pdpDefaultMetal = $pdp_default_metal ?? \App\Services\JewelryPdpMediaService::DEFAULT_METAL_KEY;
    $pdpHasViewer = ! empty($pdp_has_viewer);
    $pdpDefaultMetalUrls = \App\Services\JewelryPdpMediaService::sanitizeUrlList(
        $pdp_default_metal_urls ?? ($pdpMetalImages[$pdpDefaultMetal] ?? [])
    );
    $pdpThumbPlaceholder = \App\Services\JewelryPdpMediaService::placeholderImageUrl();
    $pdpMetalLabel = str_replace(' GOLD', ' Gold', $pdpDefaultMetal);
@endphp

<div class="pdp-metal-gallery ij-pdp-metal-gallery" data-metal-gallery>
    <div class="pdp-metal-gallery__label small text-uppercase text-muted mb-2 fw-medium letter-spacing" data-metal-label>
        {{ $pdpMetalLabel }}
    </div>
    <div class="pdp-metal-thumbs" data-metal-thumbs role="list">
        @if ($pdpHasViewer)
            <button
                type="button"
                class="pdp-media-thumb pdp-media-thumb--viewer is-active"
                data-media-thumb
                data-mode="viewer"
                aria-label="{{ __('View 3D model') }}"
                role="listitem"
            >
                <span class="pdp-media-thumb__viewer-inner" aria-hidden="true">
                    <svg class="pdp-media-thumb__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M4 7.5l8 4.5 8-4.5M4 16.5l8 4.5 8-4.5"/>
                    </svg>
                    <span class="pdp-media-thumb__badge">{{ __('3D') }}</span>
                </span>
            </button>
        @endif
        @foreach ($pdpDefaultMetalUrls as $index => $url)
            <button
                type="button"
                class="pdp-media-thumb pdp-media-thumb--image {{ ! $pdpHasViewer && $index === 0 ? 'is-active' : '' }}"
                data-media-thumb
                data-mode="image"
                data-url="{{ $url }}"
                aria-label="{{ __('View product image') }} {{ $index + 1 }}"
                role="listitem"
            >
                <img
                    src="{{ $url }}"
                    alt=""
                    class="pdp-media-thumb__img"
                    loading="lazy"
                    decoding="async"
                    onerror="this.onerror=null;this.src='{{ $pdpThumbPlaceholder }}';"
                >
            </button>
        @endforeach
    </div>
</div>
