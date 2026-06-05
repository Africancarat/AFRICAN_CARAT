@php
    $deck = $swipeDeck ?? [];
@endphp

<div class="diamond-swipe-layer" id="diamondSwipeLayer" aria-hidden="true">
    <div class="diamond-swipe-layer__header">
        <span>{{ __('Swipe to discover') }}</span>
        <button type="button" class="btn btn-sm btn-outline-light border-0"
            onclick="luxuryCloseDiamondSwipe();">{{ __('Close') }}</button>
    </div>
    <div class="diamond-swipe-layer__stage" id="diamondSwipeStage">
        <div class="diamond-swipe-layer__hint" id="diamondSwipeHint">{{ __('Swipe left to pass · right to save') }}</div>
    </div>
    <div class="diamond-swipe-layer__actions">
        <button type="button" class="btn btn-outline-light" id="diamondSwipePassBtn">{{ __('Pass') }}</button>
        <button type="button" class="btn btn-light text-dark fw-semibold border-0" id="diamondSwipeLikeBtn">{{ __('Like') }}</button>
    </div>
</div>

<script>
(function () {
    var STORAGE_KEY = 'monteluca_diamond_swipe_prefs';
    var deckRemaining = @json(array_values($deck));
    var currentCardEl = null;
    var swipeBound = false;
    var dragMoveHook = null;
    var dragEndHook = null;

    function prefs() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : { likes: [], passes: [], shapes: {} };
        } catch (e) {
            return { likes: [], passes: [], shapes: {} };
        }
    }

    function savePrefs(p) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(p));
        } catch (e) {}
    }

    function trackShape(line) {
        if (!line || typeof line !== 'string') return;
        var shape = line.split('·')[0];
        if (!shape) return;
        shape = shape.trim();
        var p = prefs();
        p.shapes[shape] = (p.shapes[shape] || 0) + 1;
        savePrefs(p);
    }

    window.renderDiamondSwipeRecommendations = function () {
        var el = document.getElementById('diamondSwipeReco');
        if (!el) return;
        var p = prefs();
        var keys = Object.keys(p.shapes || {});
        var bodyEl = el.querySelector('[data-swipe-reco-body]');
        if (!bodyEl || keys.length === 0) {
            el.classList.remove('is-visible');
            return;
        }
        keys.sort(function (a, b) {
            return (p.shapes[b] || 0) - (p.shapes[a] || 0);
        });
        var top = keys[0];
        var ix = @json(route('diamonds.index'));
        bodyEl.innerHTML =
            @json(__('Shapes you leaned toward')) + ' <strong>' + top + '</strong>. ' +
            '<a href="' + ix + '?shape=' + encodeURIComponent(top) + '">' +
            @json(__('Browse similar diamonds')) + '</a>';
        el.classList.add('is-visible');
    };

    window.luxurySetSwipeDeckFromServer = function (deck) {
        deckRemaining = Array.isArray(deck) ? deck.slice() : [];
    };

    function createCard(card) {
        var div = document.createElement('article');
        div.className = 'diamond-swipe-card';
        div.style.position = 'absolute';
        div.style.left = '50%';
        div.style.top = '8%';
        div.style.marginLeft = '0';
        div.style.transform = 'translateX(-50%)';
        div.innerHTML =
            '<div class="diamond-swipe-card__img"><img src="' + String(card.img) + '" alt="" loading="lazy"></div>' +
            '<div class="diamond-swipe-card__body">' +
            '<div class="diamond-swipe-card__title">' + String(card.name || '') + '</div>' +
            (card.line ? '<p class="diamond-swipe-card__line">' + String(card.line) + '</p>' : '') +
            '<p class="diamond-swipe-card__price">' + String(card.price || '') + '</p>' +
            '<a class="btn btn-outline-dark btn-sm mt-2" href="' + String(card.href) + '">{{ __('Details') }}</a>' +
            '</div>';
        return div;
    }

    function tearDownDragHooks() {
        if (dragMoveHook) window.removeEventListener('mousemove', dragMoveHook);
        if (dragEndHook) window.removeEventListener('mouseup', dragEndHook);
        dragMoveHook = dragEndHook = null;
    }

    function finalizeSwipe(dir, current) {
        tearDownDragHooks();
        var p = prefs();
        if (dir === 'like' && current && current.id !== undefined) {
            if (p.likes.indexOf(current.id) === -1) p.likes.push(current.id);
            trackShape(current.line);
            savePrefs(p);
        }
        if (dir === 'pass' && current && current.id !== undefined) {
            if (p.passes.indexOf(current.id) === -1) p.passes.push(current.id);
            savePrefs(p);
        }

        deckRemaining.shift();
        if (currentCardEl && currentCardEl.parentNode) {
            currentCardEl.parentNode.removeChild(currentCardEl);
        }
        currentCardEl = null;
        renderTopCard();

        window.renderDiamondSwipeRecommendations && window.renderDiamondSwipeRecommendations();
    }

    function renderTopCard() {
        var stage = document.getElementById('diamondSwipeStage');
        var hint = document.getElementById('diamondSwipeHint');
        if (!stage) return;

        while (stage.querySelector('.diamond-swipe-card')) {
            stage.removeChild(stage.querySelector('.diamond-swipe-card'));
        }

        var top = deckRemaining[0];
        if (!top) {
            if (hint) {
                hint.textContent = @json(__('You are caught up — adjust filters or close.'));
            }
            return;
        }

        currentCardEl = createCard(top);
        stage.insertBefore(currentCardEl, hint ? hint.nextSibling : null);

        var startX = 0,
            curX = 0,
            active = false;
        var threshold = Math.min(stage.clientWidth || 320, 120);

        function dragStart(ev) {
            active = true;
            var pt = ev.touches ? ev.touches[0] : ev;
            startX = pt.clientX;
            curX = 0;
        }

        function dragMove(ev) {
            if (!active || !currentCardEl) return;
            var pt = ev.touches ? ev.touches[0] : ev;
            curX = pt.clientX - startX;
            var rot = Math.max(-16, Math.min(16, curX / 16));
            currentCardEl.style.transform =
                'translateX(calc(-50% + ' + curX + 'px)) rotate(' + rot + 'deg)';
        }

        function dragEnd() {
            if (!active) return;
            active = false;
            if (curX > threshold) finalizeSwipe('like', top);
            else if (curX < -threshold) finalizeSwipe('pass', top);
            else if (currentCardEl) {
                currentCardEl.style.transform = 'translateX(-50%) rotate(0deg)';
                tearDownDragHooks();
            }
        }

        currentCardEl.addEventListener('mousedown', dragStart);
        currentCardEl.addEventListener('touchstart', dragStart, { passive: true });

        dragMoveHook = dragMove;
        dragEndHook = dragEnd;
        window.addEventListener('mousemove', dragMoveHook, { passive: true });
        window.addEventListener('mouseup', dragEndHook);

        currentCardEl.addEventListener('touchmove', dragMove, { passive: true });
        currentCardEl.addEventListener('touchend', dragEnd, { passive: true });
        currentCardEl.addEventListener('mouseup', dragEnd);
    }

    function wireSwipeActions() {
        if (swipeBound) return;
        swipeBound = true;
        var likeBtn = document.getElementById('diamondSwipeLikeBtn');
        var passBtn = document.getElementById('diamondSwipePassBtn');
        if (likeBtn) {
            likeBtn.addEventListener('click', function () {
                if (deckRemaining[0]) finalizeSwipe('like', deckRemaining[0]);
            });
        }
        if (passBtn) {
            passBtn.addEventListener('click', function () {
                if (deckRemaining[0]) finalizeSwipe('pass', deckRemaining[0]);
            });
        }
    }

    window.luxuryOpenDiamondSwipe = function () {
        wireSwipeActions();
        document.body.classList.add('diamond-swipe-active');
        var ly = document.getElementById('diamondSwipeLayer');
        if (ly) ly.setAttribute('aria-hidden', 'false');
        var hi = document.getElementById('diamondSwipeHint');
        if (hi) hi.textContent = @json(__('Swipe left to pass · right to save'));
        renderTopCard();
    };

    window.luxuryCloseDiamondSwipe = function () {
        tearDownDragHooks();
        document.body.classList.remove('diamond-swipe-active');
        var ly = document.getElementById('diamondSwipeLayer');
        if (ly) ly.setAttribute('aria-hidden', 'true');
    };

    window.renderDiamondSwipeRecommendations();
})();
</script>
