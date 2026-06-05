@extends('master.front')

@section('title', __('Diamond search'))

@section('content')
    @php $diamondSearchUrl = route('diamonds.index'); @endphp
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a></li>
                        <li class="separator"></li>
                        <li>{{ __('Diamonds') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form id="filterForm" class="diamond-search container-fluid" action="{{ $diamondSearchUrl }}" method="get" autocomplete="off">
        <div class="diamond-search__toolbar mb-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <label class="small text-muted mb-0">{{ __('Sort') }}</label>
                <select name="sort" id="diamondSort" class="form-select form-select-sm diamond-search__sort">
                    <option value="price_asc" @selected(request('sort', 'price_asc') === 'price_asc')>{{ __('Price: low to high') }}</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('Price: high to low') }}</option>
                    <option value="carat_asc" @selected(request('sort') === 'carat_asc')>{{ __('Carat: ascending') }}</option>
                    <option value="carat_desc" @selected(request('sort') === 'carat_desc')>{{ __('Carat: descending') }}</option>
                </select>
            </div>
            <div class="d-flex gap-2 flex-wrap diamond-swipe-toggle align-items-center">
                <button type="button" class="btn btn-sm btn-outline-dark d-lg-none"
                    onclick="luxuryOpenDiamondSwipe();">{{ __('Swipe discover') }}</button>
                <a href="{{ route('diamonds.compare.index') }}" class="btn btn-sm btn-outline-dark">{{ __('Diamond compare') }}</a>
                <button type="button" id="diamondOpenFiltersBtn" class="btn btn-sm btn-outline-secondary d-lg-none"
                    onclick="openDiamondFilterSheet()">
                    {{ __('Filters') }}
                    <span class="filter-badge filter-badge--corner" id="diamondFilterCountBadge" hidden>0</span>
                </button>
            </div>
        </div>

        <div id="diamondSwipeReco" class="diamond-swipe-reco mb-4">
            <div class="small text-uppercase letter-spacing">{{ __('Diamonds you might love') }}</div>
            <div data-swipe-reco-body class="small text-muted mb-0"></div>
            <div class="diamond-swipe-reco__links small"></div>
        </div>

        <div class="filter-sheet-backdrop d-lg-none" id="filterBackdrop" onclick="closeDiamondFilterSheet()"></div>

        <div class="row">
            <aside class="col-12 col-lg-3 px-0 px-lg-2 mb-4 mb-lg-0" id="filterSidebarWrap">
                <div class="diamond-filter h-100" id="filterSidebar">
                <div class="filter-sheet__handle d-lg-none"></div>
                @include('front.diamonds.partials.filter-fields')
                <button type="button" onclick="applyDiamondFilters()" class="btn-luxury w-100 mt-3">{{ __('Apply filters') }}</button>
                <button type="button"
                        onclick="closeDiamondFilterSheet()"
                        class="btn-luxury w-100 mt-2 mb-4 d-lg-none">{{ __('Done') }}</button>
                </div>
            </aside>

            <main class="col-12 col-lg-9">
                <div id="diamondGrid">
                    @include('front.diamonds.partials.grid')
                </div>
                <div id="paginationArea">
                    @include('front.diamonds.partials.pagination')
                </div>
            </main>
        </div>
    </form>

    @include('front.diamonds.partials.swipe-discover')

    <script>
        (function () {
            window.diamondSearchUrl = @json($diamondSearchUrl);

            window.updateDiamondFilterBadge = function () {
                var form = document.getElementById('filterForm');
                var badge = document.getElementById('diamondFilterCountBadge');
                if (!form || !badge) return;
                var seen = Object.create(null);
                var fd = new FormData(form);
                fd.forEach(function (v, k) {
                    if (k === 'sort' || k === 'page') return;
                    var raw = typeof v === 'string' ? v.trim() : v;
                    if (raw === '' || raw === null || raw === undefined) return;
                    var base = k.replace(/\[\]$/, '');
                    seen[base] = true;
                });
                var n = Object.keys(seen).length;
                badge.textContent = String(n);
                badge.hidden = n === 0;
            };

            window.stripEmptyDiamondParams = function (params) {
                ['lab_grown', 'shape'].forEach(function (k) {
                    var v = params.get(k);
                    if (v === null || v === '') {
                        params.delete(k);
                    }
                });
                return params;
            };

            window.applyDiamondFilters = function () {
                var form = document.getElementById('filterForm');
                var params = stripEmptyDiamondParams(new URLSearchParams(new FormData(form)));
                fetch(window.diamondSearchUrl + '?' + params.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                        },
                    })
                    .then(function (r) {
                        return r.json();
                    })
                    .then(function (data) {
                        document.getElementById('diamondGrid').innerHTML = data.html;
                        document.getElementById('paginationArea').innerHTML = data.pagination;
                        history.pushState(null, '', window.diamondSearchUrl + '?' + params.toString());
                        if (data.swipe_deck && typeof window.luxurySetSwipeDeckFromServer === 'function') {
                            window.luxurySetSwipeDeckFromServer(data.swipe_deck);
                        }
                        if (typeof window.updateDiamondFilterBadge === 'function') {
                            window.updateDiamondFilterBadge();
                        }
                        if (typeof window.renderDiamondSwipeRecommendations === 'function') {
                            window.renderDiamondSwipeRecommendations();
                        }
                    })
                    .catch(function () {});
            };

            window.setLabToggle = function (val) {
                var input = document.getElementById('labGrownInput');
                document.querySelectorAll('.filter-group--toggle .lab-toggle').forEach(function (b) {
                    b.classList.remove('active');
                });
                // Site is lab-grown only: force lab_grown=1 and keep toggle locked.
                input.setAttribute('name', 'lab_grown');
                input.value = '1';
                var labBtn = document.getElementById('labToggleLab');
                if (labBtn) labBtn.classList.add('active');
                applyDiamondFilters();
            };

            window.selectDiamondShape = function (ev, shape) {
                if (ev) ev.preventDefault();
                var input = document.getElementById('diamondShapeInput');
                if (!input) return;
                document.querySelectorAll('.shape-icon-btn').forEach(function (b) {
                    b.classList.toggle('shape-icon-btn--active', b.getAttribute('data-shape') === shape);
                });
                input.value = shape;
                if (shape === '') {
                    input.removeAttribute('name');
                } else {
                    input.setAttribute('name', 'shape');
                }
                applyDiamondFilters();
            };

            window.openDiamondFilterSheet = function () {
                document.body.classList.add('diamond-filter-open');
                document.getElementById('filterBackdrop').classList.add('open');
                document.body.style.overflow = 'hidden';
            };

            window.closeDiamondFilterSheet = function () {
                document.body.classList.remove('diamond-filter-open');
                document.getElementById('filterBackdrop').classList.remove('open');
                document.body.style.overflow = '';
            };

            window.addDiamondCompare = function (itemId) {
                fetch(@json(route('diamonds.compare.add')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ item_id: itemId }),
                })
                    .then(function (r) {
                        return r.json();
                    })
                    .then(function (data) {
                        if (typeof data.count !== 'undefined') {
                            document.querySelectorAll('.compare_count').forEach(function (el) {
                                el.textContent = String(data.count);
                            });
                        }
                        if (window.iziToast && data.message) {
                            iziToast.info({ message: data.message, position: 'topRight', timeout: 3200 });
                        } else if (data.message) {
                            alert(data.message);
                        }
                    })
                    .catch(function () {});
            };

            document.getElementById('diamondSort').addEventListener('change', function () {
                applyDiamondFilters();
            });

            var pag = document.getElementById('paginationArea');
            if (pag) {
                pag.addEventListener('click', function (e) {
                    var a = e.target.closest('a[href]');
                    if (!a) return;
                    var u;
                    try {
                        u = new URL(a.href, window.location.origin);
                    } catch (err) {
                        return;
                    }
                    if (u.pathname.indexOf('/diamonds') === -1) return;
                    e.preventDefault();
                    fetch(a.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json',
                            },
                        })
                        .then(function (r) {
                            return r.json();
                        })
                        .then(function (data) {
                            document.getElementById('diamondGrid').innerHTML = data.html;
                            pag.innerHTML = data.pagination;
                            history.pushState(null, '', a.href);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            if (data.swipe_deck && typeof window.luxurySetSwipeDeckFromServer === 'function') {
                                window.luxurySetSwipeDeckFromServer(data.swipe_deck);
                            }
                        })
                        .catch(function () {
                            window.location.href = a.href;
                        });
                });
            }

            window.updateDiamondFilterBadge();
            var ff = document.getElementById('filterForm');
            if (ff) {
                ff.addEventListener('change', window.updateDiamondFilterBadge);
                ff.addEventListener('input', window.updateDiamondFilterBadge);
            }
        })();
    </script>
@endsection
