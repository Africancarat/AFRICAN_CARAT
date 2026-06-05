{{-- Pseudo-360: frame sequence + touch drag to rotate + pinch up to 40× on the image --}}
@php
    /** @var \Illuminate\Support\Collection|array $frameUrls Absolute image URLs */
    $frameUrls = collect($frameUrls ?? [])->filter()->values();
@endphp

@if($frameUrls->count() >= 2)
    <div class="diamond-spin" id="{{ $spinId ?? 'diamondSpin' }}">
        <div class="diamond-spin__stage" data-spin-stage>
            <div class="diamond-spin__imgWrap" data-spin-wrap>
                <img src="{{ $frameUrls[0] }}" alt="" class="diamond-spin__img" data-spin-img loading="lazy"
                     width="600" height="600"
                     draggable="false">
            </div>
            <div class="diamond-spin__zoom-hint">{{ __('Drag to rotate · pinch to zoom') }}</div>
        </div>
        <div class="diamond-spin__controls">
            <input type="range" class="form-range diamond-spin__range"
                   min="0"
                   max="{{ $frameUrls->count() - 1 }}"
                   value="0"
                   aria-label="{{ __('Diamond rotation angle') }}"
                   data-frames='@json($frameUrls)'>
        </div>
    </div>
    <script>
        (function () {
            var MAX_Z = 40;
            var root = document.getElementById(@json($spinId ?? 'diamondSpin'));
            if (!root) return;
            var range = root.querySelector('.diamond-spin__range');
            var stage = root.querySelector('[data-spin-stage]');
            var wrap = root.querySelector('[data-spin-wrap]');
            var img = root.querySelector('[data-spin-img]');
            if (!range || !stage || !wrap || !img) return;
            var frames = JSON.parse(range.getAttribute('data-frames') || '[]');
            var frameIdx = 0;
            var scale = 1;
            var startDist = 0;
            var startScale = 1;
            var lastX = 0;
            var dragging = false;

            function applyFrame(i) {
                i = Math.max(0, Math.min(frames.length - 1, i));
                frameIdx = i;
                range.value = String(i);
                if (frames[i]) img.src = frames[i];
            }

            range.addEventListener('input', function () {
                applyFrame(parseInt(range.value, 10) || 0);
            });

            function pinchDist(ev) {
                if (!ev.touches || ev.touches.length < 2) return 0;
                var a = ev.touches[0],
                    b = ev.touches[1];
                var dx = a.clientX - b.clientX,
                    dy = a.clientY - b.clientY;
                return Math.sqrt(dx * dx + dy * dy);
            }

            function applyTransform() {
                wrap.style.transform = 'scale(' + scale + ')';
            }

            stage.addEventListener(
                'touchstart',
                function (ev) {
                    if (ev.touches.length === 2) {
                        ev.preventDefault();
                        startDist = pinchDist(ev);
                        startScale = scale;
                    }
                    if (ev.touches.length === 1 && scale <= 1.02) {
                        dragging = true;
                        lastX = ev.touches[0].clientX;
                    }
                },
                { passive: false }
            );

            stage.addEventListener(
                'touchmove',
                function (ev) {
                    if (ev.touches.length === 2 && startDist > 0) {
                        ev.preventDefault();
                        var d = pinchDist(ev);
                        if (d > 0 && startDist > 0) {
                            var next = (startScale * d) / startDist;
                            scale = Math.min(MAX_Z, Math.max(1, next));
                            applyTransform();
                        }
                    }
                    if (dragging && scale <= 1.02 && ev.touches.length === 1) {
                        var x = ev.touches[0].clientX;
                        var dx = x - lastX;
                        lastX = x;
                        var per = stage.clientWidth || 320;
                        var delta = -(dx / Math.max(per * 0.35, 120)) * frames.length;
                        var nextIdx = Math.round(frameIdx + delta);
                        applyFrame(nextIdx);
                    }
                },
                { passive: false }
            );

            stage.addEventListener('touchend', function (ev) {
                if (ev.touches.length < 2) startDist = 0;
                if (!ev.touches.length) dragging = false;
                if (scale < 1.01) {
                    scale = 1;
                    applyTransform();
                }
            });

            /* Mouse drag fallback (desktop QA) */
            var mDown = false,
                mLast = 0;
            stage.addEventListener('mousedown', function (e) {
                if (e.button !== 0) return;
                mDown = true;
                mLast = e.clientX;
            });
            window.addEventListener('mouseup', function () {
                mDown = false;
            });
            stage.addEventListener('mousemove', function (e) {
                if (!mDown || scale > 1.02) return;
                var dx = e.clientX - mLast;
                mLast = e.clientX;
                var per = stage.clientWidth || 320;
                var delta = -(dx / Math.max(per * 0.35, 120)) * frames.length;
                applyFrame(Math.round(frameIdx + delta));
            });
        })();
    </script>
@endif
