{{-- PDP left column: 360/spin PRIMARY; stills + carousel SECONDARY --}}
@php
    $da = $item->diamondAttribute;
    $spinFrames = collect($da && is_array($da->images_360 ?? null) ? $da->images_360 : [])
        ->map(fn ($p) => is_string($p) ? \App\Helpers\ImageHelper::storageImageUrl($p) : '')
        ->filter()
        ->values();
    $useSpin = $da && $spinFrames->count() >= 2 && blank($da->video_360_url ?? null);
    $hasDiamondPrimary = $da && (filled($da->video_360_url) || $useSpin);
@endphp

@if ($item->video && ! $hasDiamondPrimary)
    <div class="gallery-wrapper">
        <div class="gallery-item video-btn text-center">
            <a href="{{ $item->video }}" title="Watch video"></a>
        </div>
    </div>
@endif

@php
    $v360Raw = trim((string) ($da->video_360_url ?? ''));
    $native360Video = $v360Raw !== '' && (bool) preg_match('/\.(mp4|webm|mov)(\?|#|$)/i', $v360Raw);
@endphp
@if ($hasDiamondPrimary)
    {{-- Critical: rotating viewer dominates the PDP --}}
    <div class="pdp-media-primary pdp-media-primary--diamond">
        @if (filled($da->video_360_url) && $native360Video)
            <div class="viewer-360 viewer-360--hero" id="luxury360nativeRoot">
                <div style="position:absolute;inset:0;overflow:hidden;" data-zoom-wrap>
                    <video id="luxuryNative360video"
                           class="viewer-360__native-video"
                           src="{{ $da->video_360_url }}"
                           playsinline
                           webkit-playsinline
                           muted
                           loop
                           autoplay
                           preload="metadata"></video>
                </div>
                <p class="viewer-360__hint">&#8635; {{ __('Swipe horizontally to scrub rotation · pinch to zoom') }}</p>
            </div>
            <script>
                (function () {
                    var v = document.getElementById('luxuryNative360video');
                    var root = document.getElementById('luxury360nativeRoot');
                    var wrap = root && root.querySelector('[data-zoom-wrap]');
                    if (!v || !root || !wrap) return;

                    function scrub(ev) {
                        if (!isFinite(v.duration) || !v.duration) return;
                        var rect = wrap.getBoundingClientRect();
                        var x = ('touches' in ev && ev.touches[0] ? ev.touches[0].clientX : ev.clientX) - rect.left;
                        x = Math.max(0, Math.min(rect.width, x));
                        v.currentTime = (x / Math.max(rect.width, 1)) * v.duration;
                    }

                    var scrubbing = false;
                    wrap.addEventListener('touchstart', function (e) {
                        if (e.targetTouches.length === 2) return;
                        scrubbing = true;
                        scrub(e);
                    }, { passive: true });

                    wrap.addEventListener(
                        'touchmove',
                        function (e) {
                            if (!scrubbing || e.targetTouches.length > 1) return;
                            e.preventDefault();
                            scrub(e);
                        },
                        { passive: false }
                    );

                    wrap.addEventListener('touchend', function () {
                        scrubbing = false;
                    });

                    /* Pinch zoom (up to 40× cap) via CSS scale on wrapper */
                    var MAX_Z = 40;
                    var startDist = 0;
                    var startScale = 1;
                    var scale = 1;

                    function dist(a, b) {
                        var dx = a.clientX - b.clientX;
                        var dy = a.clientY - b.clientY;
                        return Math.sqrt(dx * dx + dy * dy);
                    }

                    function applyZs() {
                        wrap.style.transform = 'scale(' + scale + ')';
                        wrap.style.transformOrigin = 'center center';
                    }

                    wrap.addEventListener('touchstart', function (e) {
                        if (e.touches.length === 2) {
                            startDist = dist(e.touches[0], e.touches[1]);
                            startScale = scale;
                        }
                    });

                    wrap.addEventListener(
                        'touchmove',
                        function (e) {
                            if (e.touches.length === 2 && startDist > 0) {
                                e.preventDefault();
                                var nd = dist(e.touches[0], e.touches[1]);
                                scale = Math.min(MAX_Z, Math.max(1, (startScale * nd) / startDist));
                                applyZs();
                            }
                        },
                        { passive: false }
                    );

                    wrap.addEventListener('touchend', function (e) {
                        if (e.touches.length < 2) startDist = 0;
                        if (scale < 1.03) {
                            scale = 1;
                            applyZs();
                        }
                    });
                })();
            </script>
        @elseif (filled($da->video_360_url))
            <div class="viewer-360 viewer-360--hero">
                <iframe
                    src="{{ $da->video_360_url }}"
                    class="viewer-360__iframe"
                    allowfullscreen
                    loading="eager"
                    title="360° Diamond View">
                </iframe>
                <p class="viewer-360__hint">&#8635; {{ __('Primary view — vendor 360 viewer') }}</p>
            </div>
        @else
            @include('front.diamonds.partials.image-rotator', [
                'frameUrls' => $spinFrames->all(),
                'spinId' => 'diamondSpinMain',
            ])
            <p class="viewer-360__hint text-center">{{ __('Primary view — drag the slider to rotate the stone.') }}</p>
        @endif
    </div>

    {{-- Secondary: still photography & gallery (always visible beneath viewer) --}}
    <div class="pdp-media-secondary mt-3">
        <p class="pdp-media-secondary__label small text-uppercase text-muted letter-spacing">{{ __('Gallery & angles') }}</p>
        <div class="image-gallery pdp-gallery-secondary-carousel" id="productGallerySecondary">
            <div class="product-thumbnails insize">
                <div class="product-details-slider owl-carousel">
                    @foreach ($pdp_slider_images ?? [] as $sliderSrc)
                        <div class="item"><img src="{{ $sliderSrc }}" alt="{{ $item->name }}"></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="gallery-thumbs pdp-gallery-thumbs-secondary">
            @foreach ($pdp_slider_images ?? [] as $sliderSrc)
                <img src="{{ $sliderSrc }}" class="gallery-thumb" alt="">
            @endforeach
        </div>
    </div>
@else
    @include('front.catalog.partials.pdp-media-stage', [
        'item' => $item,
        'pdp_metal_images' => $pdp_metal_images ?? [],
        'pdp_default_metal' => $pdp_default_metal ?? \App\Services\JewelryPdpMediaService::DEFAULT_METAL_KEY,
        'pdp_viewer_meta' => $pdp_viewer_meta ?? [],
        'pdp_show_media_gallery' => $pdp_show_media_gallery ?? false,
        'pdp_primary_still' => $pdp_primary_still ?? null,
        'pdp_slider_images' => $pdp_slider_images ?? [],
    ])

    @php
        $pdpIjMediaActive = ! empty($pdp_show_media_gallery);
        $pdpOwlImages = \App\Services\JewelryPdpMediaService::sanitizeUrlList($pdp_slider_images ?? []);
    @endphp

    {{-- Owl carousel: synced by PdpMedia when iJewel gallery active; hidden visually to avoid duplicate thumbs/main --}}
    <div class="lux-pdp-gallery-layout pdp-gallery-layout {{ $pdpIjMediaActive ? 'pdp-gallery-layout--ij-primary' : '' }}">
        <div class="pdp-gallery-main lux-pdp-gallery-main" id="pdpOwlGalleryHost">
            <div class="image-gallery lux-pdp-gallery" id="productGallery" data-lux-gallery>
                <div class="product-thumbnails insize">
                    <div class="product-details-slider owl-carousel">
                        @forelse ($pdpOwlImages as $sliderSrc)
                            <div class="item">
                                <div class="lux-zoom-wrap" data-lux-zoom>
                                    <img src="{{ $sliderSrc }}" loading="lazy" alt="" class="lux-main-img">
                                </div>
                            </div>
                        @empty
                            <div class="item">
                                <div class="lux-zoom-wrap" data-lux-zoom>
                                    <img src="{{ \App\Services\JewelryPdpMediaService::placeholderImageUrl() }}" loading="lazy" alt="" class="lux-main-img">
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @unless ($pdpIjMediaActive)
            <div class="gallery-thumbs lux-pdp-thumbs lux-pdp-thumbs--rail" data-lux-thumbs>
                @foreach ($pdpOwlImages as $sliderSrc)
                    <button type="button" class="lux-thumb" data-lux-thumb>
                        <img src="{{ $sliderSrc }}" class="gallery-thumb" loading="lazy" alt="" decoding="async">
                    </button>
                @endforeach
            </div>
        @endunless
    </div>
@endif

@once
    <style>
        .lux-pdp-gallery .lux-zoom-wrap{
            position:relative;
            overflow:hidden;
            border-radius: 10px;
            background: #fff;
        }
        .lux-pdp-gallery .lux-main-img{
            width:100%;
            height:auto;
            display:block;
            transition: opacity .18s ease, transform .18s ease;
            transform: translateZ(0);
            will-change: transform;
        }
        .lux-pdp-gallery.is-switching .lux-main-img{ opacity:.35; }

        .lux-pdp-thumbs{
            display:flex;
            gap:12px;
            align-items:center;
            overflow-x:auto;
            padding: 12px 2px 2px;
            -webkit-overflow-scrolling: touch;
        }
        .lux-pdp-thumbs::-webkit-scrollbar{ height:6px; }
        .lux-pdp-thumbs::-webkit-scrollbar-thumb{ background: rgba(0,0,0,.18); border-radius: 6px; }

        .lux-thumb{
            border:1px solid rgba(0,0,0,.12);
            background:#fff;
            padding:6px;
            border-radius:10px;
            flex: 0 0 auto;
            transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
        }
        .lux-thumb img{
            display:block;
            width:72px;
            height:72px;
            object-fit:cover;
            border-radius:8px;
        }
        .lux-thumb:hover{ transform: translateY(-1px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
        .lux-thumb.is-active{ border-color: rgba(184,134,11,.9); box-shadow: 0 0 0 2px rgba(184,134,11,.25); }

        @media (max-width: 576px){
            .lux-thumb img{ width:56px; height:56px; }
        }

        /* iJewel primary media: single stage + horizontal metal strip */
        .pdp-product-media--ij-active{ display: block; width: 100%; }
        .ij-pdp-media-stage{
            position: relative;
            min-height: 320px;
            padding: 1rem;
            background: #fff;
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 12px;
            overflow: hidden;
        }
        .pdp-media-stage__img-wrap{
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background: #fff;
            text-align: center;
        }
        .pdp-media-stage__img{
            display: block;
            max-width: 100%;
            max-height: 520px;
            width: auto;
            height: auto;
            margin: 0 auto;
            object-fit: contain;
        }
        .pdp-media-viewer{ position: relative; z-index: 1; }
        .pdp-media-viewer__iframe{ display: block; width: 100%; height: 100%; border: 0; }
        .pdp-metal-gallery{ margin-top: 1rem; clear: both; }
        .pdp-metal-thumbs{
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: stretch;
            gap: 10px;
            overflow-x: auto;
            padding: 4px 2px 8px;
            -webkit-overflow-scrolling: touch;
        }
        .pdp-media-thumb{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            width: 88px;
            height: 88px;
            margin: 0;
            padding: 4px;
            border: 2px solid rgba(0,0,0,.12);
            background: #fff;
            border-radius: 10px;
            cursor: pointer;
            vertical-align: top;
            transition: border-color .16s ease, box-shadow .16s ease;
        }
        .pdp-media-thumb.is-active{
            border-color: rgba(184,134,11,.95);
            box-shadow: 0 0 0 2px rgba(184,134,11,.25);
        }
        .pdp-media-thumb__img{
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }
        .pdp-media-thumb__viewer-inner{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: #f8fafc;
            color: #334155;
            border-radius: 6px;
        }
        .pdp-media-thumb__icon{ width: 26px; height: 26px; flex-shrink: 0; }
        .pdp-media-thumb__badge{ font-size: 10px; font-weight: 700; letter-spacing: .08em; line-height: 1; }
        .pdp-media-empty,
        .pdp-media-fallback{
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Hide legacy vertical rail + duplicate owl hero when iJewel strip is active */
        .pdp-gallery-layout--ij-primary .lux-pdp-thumbs--rail{ display: none !important; }
        .pdp-gallery-layout--ij-primary .lux-pdp-gallery-main{
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
            clip: rect(0,0,0,0) !important;
            white-space: nowrap !important;
            border: 0 !important;
            padding: 0 !important;
            margin: -1px !important;
        }
        .pdp-gallery-layout--ij-primary{
            position: relative;
            margin: 0 !important;
            min-height: 0;
        }
        @media (max-width: 576px){
            .pdp-media-thumb{ width: 72px; height: 72px; }
        }
    </style>
@endonce

@once
    <script>
        (function(){
            function qs(sel, root){ return (root||document).querySelector(sel); }
            function qsa(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }

            function getActiveMainImg(gallery){
                // OwlCarousel visible image (if initialized)
                var active = qs('.product-details-slider .owl-item.active img', gallery);
                if (active) return active;
                // Fallback markup
                return qs('.product-details-slider .item:first-child img', gallery);
            }

            function setMainImgSrc(gallery, src){
                if (!src) return;
                gallery.classList.add('is-switching');
                var img = getActiveMainImg(gallery);
                if (!img) { gallery.classList.remove('is-switching'); return; }

                // Preload for smooth swap
                var pre = new Image();
                pre.onload = function(){
                    img.src = src;
                    requestAnimationFrame(function(){
                        setTimeout(function(){ gallery.classList.remove('is-switching'); }, 160);
                    });
                };
                pre.onerror = function(){ gallery.classList.remove('is-switching'); };
                pre.src = src;
            }

            function bindThumbs(gallery, thumbs){
                var btns = qsa('[data-lux-thumb]', thumbs);
                if (!btns.length) return;

                function activate(btn){
                    btns.forEach(function(b){ b.classList.remove('is-active'); });
                    btn.classList.add('is-active');
                }

                btns.forEach(function(btn, idx){
                    var img = qs('img', btn);
                    if (!img) return;
                    if (idx === 0) btn.classList.add('is-active');

                    btn.addEventListener('click', function(){
                        activate(btn);
                        setMainImgSrc(gallery, img.getAttribute('src'));
                    });
                });
            }

            function bindZoom(gallery){
                // Premium hover zoom using transform-origin under cursor.
                var wrap = qs('[data-lux-zoom]', gallery);
                if (!wrap) return;
                var img = qs('img', wrap);
                if (!img) return;

                var raf = 0;
                function onMove(e){
                    if (raf) return;
                    raf = requestAnimationFrame(function(){
                        raf = 0;
                        var r = wrap.getBoundingClientRect();
                        var x = ((e.clientX - r.left) / Math.max(r.width, 1)) * 100;
                        var y = ((e.clientY - r.top) / Math.max(r.height, 1)) * 100;
                        img.style.transformOrigin = x + '% ' + y + '%';
                        img.style.transform = 'scale(1.85)';
                    });
                }
                function onLeave(){
                    img.style.transformOrigin = '50% 50%';
                    img.style.transform = 'scale(1)';
                }

                wrap.addEventListener('mousemove', onMove);
                wrap.addEventListener('mouseleave', onLeave);
                wrap.addEventListener('touchstart', onLeave, {passive:true});
            }

            function bindStageZoom(){
                var wrap = qs('[data-product-media] [data-media-image-wrap][data-lux-zoom]');
                if (!wrap) return;
                var img = qs('[data-media-image]', wrap);
                if (!img) return;
                var raf = 0;
                function onMove(e){
                    if (raf) return;
                    raf = requestAnimationFrame(function(){
                        raf = 0;
                        var r = wrap.getBoundingClientRect();
                        var x = ((e.clientX - r.left) / Math.max(r.width, 1)) * 100;
                        var y = ((e.clientY - r.top) / Math.max(r.height, 1)) * 100;
                        img.style.transformOrigin = x + '% ' + y + '%';
                        img.style.transform = 'scale(1.85)';
                    });
                }
                function onLeave(){
                    img.style.transformOrigin = '50% 50%';
                    img.style.transform = 'scale(1)';
                }
                wrap.addEventListener('mousemove', onMove);
                wrap.addEventListener('mouseleave', onLeave);
                wrap.addEventListener('touchstart', onLeave, {passive:true});
            }

            function init(){
                var gallery = qs('[data-lux-gallery]');
                var thumbs = qs('[data-lux-thumbs]');
                var ijActive = !!qs('[data-product-media].pdp-product-media--ij-active');
                if (gallery && thumbs && !ijActive) {
                    bindThumbs(gallery, thumbs);
                    bindZoom(gallery);
                }
                if (ijActive) {
                    bindStageZoom();
                } else if (gallery) {
                    bindZoom(gallery);
                }
            }

            if (document.readyState === 'loading'){
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Re-init after OwlCarousel refreshes / metal gallery swaps (safe no-ops).
            window.__luxPdpGalleryInit = init;
        })();
    </script>
@endonce

@once
    <script src="{{ asset('assets/front/js/pdp-media.js') }}"></script>
@endonce

@if (filled($item->pdp_ar_model_url ?? null))
    <div class="pdp-webar mt-3">
        <p class="small text-muted text-uppercase letter-spacing mb-2">{{ __('Virtual try-on (AR)') }}</p>
        <script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@3.5.0/dist/model-viewer.min.js"></script>
        <model-viewer id="pdpModelViewer"
            src="{{ $item->pdp_ar_model_url }}"
            alt="{{ $item->name }}"
            camera-controls
            touch-action="pan-y"
            ar
            ar-modes="webxr scene-viewer quick-look"
            shadow-intensity="1"
            style="width:100%;height:340px;background:#f5f5f3;border:1px solid #ece8e0;">
        </model-viewer>
    </div>
@endif
