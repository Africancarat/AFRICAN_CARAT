@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')


   

    @php
        $hasHomeHeroVideo = is_file(public_path('storage/images/African_Carat_Ads_3.mp4'));
    @endphp
    @if ($extra_settings->is_t3_slider == 1 || $hasHomeHeroVideo)
        @include('components.home-luxury-hero')
    @endif

    @if ($extra_settings->is_t3_service_section == 1)
        <section class="service-section mt-30 pt-0">
            <div class="container">
                <div class="row">
                    @foreach ($services as $service)
                        <div class="col-lg-3 col-sm-6 text-center mb-30">
                            <div class="single-service single-service2">
                                <img src="{{ \App\Helpers\HomePageImageHelper::serviceUrl($service->photo, $loop->index) }}" alt="{{ $service->title }}">
                                <div class="content">
                                    <h6 class="mb-2">{{ $service->title }}</h6>
                                    <p class="text-sm text-muted mb-0">{{ $service->details }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($extra_settings->is_t3_3_column_banner_first == 1)
        @include('front.partials.home-first-banner-strip', ['banner_first' => $banner_first])
    @endif

    @php
        $sb = $split_path_banner ?? [];
        $hasAfricanCaratEngagementImage = is_file(public_path('storage/images/African carat/2.png'));
        $showSplitBanner = $hasAfricanCaratEngagementImage
            || (($extra_settings->is_t3_split_path_banner ?? 0) == 1
                && (
                    ($sb['headline'] ?? '') !== '' || ($sb['kicker'] ?? '') !== '' || ($sb['body'] ?? '') !== ''
                    || ($sb['fg_image'] ?? '') !== '' || ($sb['bg_image'] ?? '') !== ''
                    || ($sb['btn1_label'] ?? '') !== '' || ($sb['btn2_label'] ?? '') !== ''
                ));
    @endphp
    @if ($showSplitBanner)
        @include('front.partials.home-split-path-banner', ['split' => $sb])
    @endif

<section class="custom-trust">
        <div class="container">
            <h2 class="trust-heading">{{ __('Trusted by private clients across global markets.') }}</h2>
            <div class="trust-grid">
                <div class="trust-item">
                    <img class="trust-photo trust-photo--igi" src="{{ \App\Helpers\HomePageImageHelper::trustUrl('igi') }}" alt="{{ __('IGI Certified') }}" loading="lazy">
                    <p>{{ __('IGI Certified') }}</p>
                </div>
                <div class="trust-item">
                    <img class="trust-photo trust-photo--checkout" src="{{ \App\Helpers\HomePageImageHelper::trustUrl('checkout') }}" alt="{{ __('Secure Checkout') }}" loading="lazy">
                    <p>{{ __('Secure Checkout') }}</p>
                </div>
                <div class="trust-item">
                    <img class="trust-photo trust-photo--large" src="{{ \App\Helpers\HomePageImageHelper::trustUrl('delivery') }}" alt="{{ __('Global Delivery') }}" loading="lazy">
                    <p>{{ __('Global Delivery') }}</p>
                </div>
                <div class="trust-item">
                    <img class="trust-photo trust-photo--large" src="{{ \App\Helpers\HomePageImageHelper::trustUrl('consultation') }}" alt="{{ __('Private Consultation Available') }}" loading="lazy">
                    <p>{{ __('Private Consultation Available') }}</p>
                </div>
            </div>
        </div>
    </section>

    @php
        $trendingPicks = (clone $products)->where('is_type', 'feature')->orderBy('id', 'DESC')->take(8)->get();
        if ($trendingPicks->isEmpty()) {
            $trendingPicks = (clone $products)->orderBy('id', 'DESC')->take(8)->get();
        }
    @endphp
    @if ($trendingPicks->count())
        <section class="luxury-trending-picks">
            <div class="container luxury-trending-picks__inner">
                <div class="luxury-trending-picks__intro">
                    <h3>{{ __('Trending Picks') }}</h3>
                    <p>{{ __('Crafted for modern legacy') }}</p>
                </div>
                <div class="luxury-trending-picks__carousel" data-trending-carousel>
                    <button type="button" class="luxury-trending-picks__arrow luxury-trending-picks__arrow--prev" data-trending-prev aria-label="{{ __('Previous products') }}">‹</button>
                    <div class="luxury-trending-picks__rail" role="region" aria-label="{{ __('Trending picks') }}" data-trending-rail>
                        @foreach ($trendingPicks as $item)
                            <article class="luxury-trending-pick-card">
                                <a href="{{ route('front.product', $item->slug) }}" class="luxury-trending-pick-card__thumb-wrap">
                                    <span class="luxury-trending-pick-card__badge">{{ __('New') }}</span>
                                    <img class="luxury-trending-pick-card__thumb"
                                         src="{{ \App\Helpers\HomePageImageHelper::trendingPickUrl($item->thumbnail ?: $item->photo, $loop->index) }}"
                                         alt="{{ $item->name }}"
                                         loading="lazy">
                                </a>
                                <h4 class="luxury-trending-pick-card__title">
                                    <a href="{{ route('front.product', $item->slug) }}">{{ Str::limit($item->name, 54) }}</a>
                                </h4>
                                <p class="luxury-trending-pick-card__price">
                                    {{ __('Starting from') }} {{ PriceHelper::grandCurrencyPrice($item) }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                    <button type="button" class="luxury-trending-picks__arrow luxury-trending-picks__arrow--next" data-trending-next aria-label="{{ __('Next products') }}">›</button>
                </div>
            </div>
        </section>
        <script>
            (function () {
                var root = document.querySelector('[data-trending-carousel]');
                if (!root) return;
                var rail = root.querySelector('[data-trending-rail]');
                var prev = root.querySelector('[data-trending-prev]');
                var next = root.querySelector('[data-trending-next]');
                if (!rail || !prev || !next) return;

                function step() {
                    var card = rail.querySelector('.luxury-trending-pick-card');
                    if (!card) return rail.clientWidth * 0.8;
                    var styles = window.getComputedStyle(rail);
                    var gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
                    return card.getBoundingClientRect().width + gap;
                }

                prev.addEventListener('click', function () {
                    rail.scrollBy({ left: -step(), behavior: 'smooth' });
                });
                next.addEventListener('click', function () {
                    rail.scrollBy({ left: step(), behavior: 'smooth' });
                });
            })();
        </script>
    @endif

    @if ($extra_settings->is_t3_pecialpick == 1)
        @php
            $inspectionImage = \App\Helpers\HomePageImageHelper::inspectionUrl();
        @endphp
        <section class="luxury-inspection-section">
            <div class="container">
                <div class="luxury-inspection">
                    <div class="luxury-inspection__media">
                        <img src="{{ $inspectionImage }}" alt="{{ __('Real-time interactive diamond inspection') }}" loading="lazy">
                    </div>
                    <div class="luxury-inspection__content">
                        <h2>{{ __('Real-time interactive') }}<br>{{ __('Diamond Inspection') }}</h2>
                        <p>
                            {{ __('Take a closer look at your favorite diamonds using our Real-Time Diamond Inspection service; a one-on-one consultation with an uncommissioned certified gemologist. Share your screen and get expert guidance as you explore diamonds in 360° HD with up to 40x magnification.') }}
                        </p>
                        <a href="/education" class="luxury-inspection__link">
                            {{ __('Start your diamond inspection now') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="luxury-diamond-revolution">
            @php
                $diamondSlides = [
                    [
                        'slug' => 'emerald',
                        'title' => __('Emerald Diamond'),
                        'desc' => __('Classic rectangular cut with step facets and long clean lines'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('emerald'),
                    ],
                    [
                        'slug' => 'asscher',
                        'title' => __('Asscher Diamond'),
                        'desc' => __('Vintage-style square shape with cropped corners'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('asscher'),
                    ],
                    [
                        'slug' => 'oval',
                        'title' => __('Oval Diamond'),
                        'desc' => __('Elongated brilliance that flatters the finger beautifully'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('oval'),
                    ],
                    [
                        'slug' => 'round',
                        'title' => __('Round Diamond'),
                        'desc' => __('The timeless brilliant cut with maximum sparkle'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('round'),
                    ],
                    [
                        'slug' => 'cushion',
                        'title' => __('Cushion Diamond'),
                        'desc' => __('Soft rounded corners with a romantic pillow-like silhouette'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('cushion'),
                    ],
                    [
                        'slug' => 'heart',
                        'title' => __('Heart Diamond'),
                        'desc' => __('Symbolic and expressive shape with bold fire'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('heart'),
                    ],
                    [
                        'slug' => 'marquise',
                        'title' => __('Marquise Diamond'),
                        'desc' => __('Long, narrow surface makes it appear larger than life'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('marquise'),
                    ],
                    [
                        'slug' => 'pear',
                        'title' => __('Pear Diamond'),
                        'desc' => __('A graceful blend of round brilliance and marquise elegance'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('pear'),
                    ],
                    [
                        'slug' => 'princess',
                        'title' => __('Princess Diamond'),
                        'desc' => __('Modern square cut with bright, sharp scintillation'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('princess'),
                    ],
                    [
                        'slug' => 'radiant',
                        'title' => __('Radiant Diamond'),
                        'desc' => __('Crisp trimmed corners with lively brilliant faceting'),
                        'image' => \App\Helpers\HomePageImageHelper::diamondShapeUrl('radiant'),
                    ],
                ];
                $initialSlide = 0;
                foreach ($diamondSlides as $idx => $slide) {
                    if ($slide['slug'] === 'marquise') {
                        $initialSlide = $idx;
                        break;
                    }
                }
            @endphp
            <div class="container">
                <div class="luxury-diamond-revolution__head text-center">
                    <p class="luxury-diamond-revolution__kicker">{{ __('Experience the') }}</p>
                    <h2>{{ __('Diamond Revolution') }}</h2>
                    <p class="luxury-diamond-revolution__sub">
                        {{ __('Spin actual diamonds in 360° HD and zoom in up to 40x. One of the world’s biggest collections of loose diamonds, at your fingertips.') }}
                    </p>
                </div>

                <div class="luxury-diamond-revolution__stage" data-diamond-revolution data-initial-index="{{ $initialSlide }}">
                    <div class="luxury-diamond-revolution__viewport">
                        @foreach ($diamondSlides as $idx => $slide)
                            <a href="{{ route('diamonds.index') . '?shape=' . $slide['slug'] }}"
                               class="luxury-diamond-revolution__shape-slide{{ $idx === $initialSlide ? ' is-active' : '' }}"
                               data-shape-slide
                               data-shape-title="{{ $slide['title'] }}"
                               data-shape-desc="{{ $slide['desc'] }}"
                               aria-hidden="{{ $idx === $initialSlide ? 'false' : 'true' }}">
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" loading="lazy">
                            </a>
                        @endforeach
                    </div>

                    <div class="luxury-diamond-revolution__caption text-center">
                        <div class="luxury-diamond-revolution__caption-row">
                            <button type="button" class="luxury-diamond-revolution__arrow" data-shape-prev aria-label="{{ __('Previous shape') }}">←</button>
                            <h3 data-shape-title-text>{{ $diamondSlides[$initialSlide]['title'] }}</h3>
                            <button type="button" class="luxury-diamond-revolution__arrow" data-shape-next aria-label="{{ __('Next shape') }}">→</button>
                        </div>
                        <p data-shape-desc-text>{{ $diamondSlides[$initialSlide]['desc'] }}</p>
                    </div>
                </div>
            </div>
        </section>
        @php
            $crowningJewels = [
                [
                    'title' => __("Eternity Rings"),
                    'desc' => __("The ultimate symbol of lifelong commitment, eternity rings make for an ideal wedding or anniversary ring, or can be worn alongside your engagement ring."),
                    'cta' => __("Explore"),
                    'url' => route('front.catalog'),
                    'image' => \App\Helpers\HomePageImageHelper::crowningUrl('eternity'),
                    'watermark' => 'find your sparkle',
                ],
                [
                    'title' => __("Gemstone Jewelry"),
                    'desc' => __("Kissed by the colors of nature, sapphire, ruby, emerald, and moissanite jewelry makes for a stunningly exotic look."),
                    'cta' => __("Browse"),
                    'url' => route('front.catalog'),
                    'image' => \App\Helpers\HomePageImageHelper::crowningUrl('gemstone'),
                    'video' => \App\Helpers\HomePageImageHelper::crowningVideoUrl('gemstone'),
                    'watermark' => 'gemstone collection',
                ],
                [
                    'title' => __("Men's Wedding Rings"),
                    'desc' => __("From timeless to modern, choose a wedding ring in a traditional, classic, rugged carved, elegant diamond, or funky alternative metal."),
                    'cta' => __("Discover"),
                    'url' => route('front.catalog'),
                    'image' => \App\Helpers\HomePageImageHelper::crowningUrl('mens_wedding'),
                    'watermark' => "men's wedding rings",
                ],
                [
                    'title' => __("Diamond Studs"),
                    'desc' => __("The perfect gift for any occasion, these handcrafted preset diamond studs make a bold yet elegant statement."),
                    'cta' => __("Browse"),
                    'url' => route('front.catalog'),
                    'image' => \App\Helpers\HomePageImageHelper::crowningUrl('studs'),
                    'video' => \App\Helpers\HomePageImageHelper::crowningVideoUrl('studs'),
                    'watermark' => 'diamond studs',
                ],
            ];
        @endphp
        <section class="luxury-crowning-jewels">
            <div class="container">
                <div class="luxury-crowning-jewels__head text-center">
                    <p class="luxury-crowning-jewels__kicker">{{ __('The') }}</p>
                    <h2>{{ __('Crowning Jewels') }}</h2>
                    <p>{{ __('Our diamond and gemstone fine jewelry collection offers hand-crafted pieces of unforgettable luxury that are perfect for any occasion.') }}</p>
                </div>

                @foreach ($crowningJewels as $idx => $block)
                    <article class="luxury-crowning-jewels__row{{ $idx % 2 === 1 ? ' is-right' : ' is-left' }}">
                        <span class="luxury-crowning-jewels__watermark" aria-hidden="true">{{ $block['watermark'] }}</span>
                        <div class="luxury-crowning-jewels__media">
                            @if (!empty($block['video']))
                                <video class="luxury-crowning-jewels__video"
                                    autoplay
                                    muted
                                    loop
                                    playsinline
                                    preload="metadata"
                                    poster="{{ $block['image'] }}"
                                    aria-label="{{ $block['title'] }}">
                                    <source src="{{ $block['video'] }}" type="video/mp4">
                                </video>
                            @else
                                <img src="{{ $block['image'] }}" alt="{{ $block['title'] }}" loading="lazy">
                            @endif
                        </div>
                        <div class="luxury-crowning-jewels__panel">
                            <h3>{{ $block['title'] }}</h3>
                            <p>{{ $block['desc'] }}</p>
                            <a href="{{ $block['url'] }}">{{ $block['cta'] }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        <script>
            (function () {
                var root = document.querySelector('[data-diamond-revolution]');
                if (!root) return;
                var slides = Array.prototype.slice.call(root.querySelectorAll('[data-shape-slide]'));
                if (!slides.length) return;
                var titleEl = root.querySelector('[data-shape-title-text]');
                var descEl = root.querySelector('[data-shape-desc-text]');
                var prevBtn = root.querySelector('[data-shape-prev]');
                var nextBtn = root.querySelector('[data-shape-next]');
                var idx = parseInt(root.getAttribute('data-initial-index') || '0', 10);
                if (isNaN(idx) || idx < 0 || idx >= slides.length) idx = 0;

                function render() {
                    slides.forEach(function (el, i) {
                        var active = i === idx;
                        var prev = i === (idx - 1 + slides.length) % slides.length;
                        var next = i === (idx + 1) % slides.length;
                        el.classList.toggle('is-active', active);
                        el.classList.toggle('is-prev', prev);
                        el.classList.toggle('is-next', next);
                        el.classList.toggle('is-hidden', !(active || prev || next));
                        el.setAttribute('aria-hidden', active ? 'false' : 'true');
                    });
                    if (titleEl) titleEl.textContent = slides[idx].getAttribute('data-shape-title') || '';
                    if (descEl) descEl.textContent = slides[idx].getAttribute('data-shape-desc') || '';
                }

                if (prevBtn) prevBtn.addEventListener('click', function () {
                    idx = (idx - 1 + slides.length) % slides.length;
                    render();
                });
                if (nextBtn) nextBtn.addEventListener('click', function () {
                    idx = (idx + 1) % slides.length;
                    render();
                });

                render();
            })();
        </script>
    @endif


    @if ($extra_settings->is_t3_falsh == 1)
        <div class="flash-sell-new-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title section-title2 section-title3section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('Flash Deal') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-content">
                            <div class="flash-deal-slider owl-carousel" >
                                @foreach ($products->orderBy('id','DESC')->get()  as $item)
                                @if ($item->is_type == 'flash_deal' && $item->date != null)
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                <div class="product-badge bg-secondary border-default text-body
                                                ">{{__('out of stock')}}</div>
                                                @endif
                                                @if($item->previous_price && $item->previous_price !=0)
                                                <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($item)}}</div>
                                                @endif
                                                <img class="lazy" data-src="{{url('/core/public/storage/images/'.$item->thumbnail)}}" alt="Product">
                                                <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                                    @include('includes.item_footer',['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">

                                                    <div class="product-category"><a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                                                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                                        {{ Str::limit($item->name,50) }}
                                                    </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                    </div>
                                                    <h4 class="product-price">
                                                    @if ($item->previous_price != 0)
                                                    <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                                    @endif

                                                    {{PriceHelper::grandCurrencyPrice($item)}}
                                                    </h4>
                                                    @if (date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y'))
                                                    <div class="countdown countdown-alt mb-3" data-date-time="{{ $item->date }}">
                                                    </div>
                                                    @endif
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($extra_settings->is_t3_3_column_banner_second == 1)
    @php
        $s2 = $banner_secend ?? [];
        $crumb = trim((string) (($s2['breadcrumb'] ?? '') !== '' ? $s2['breadcrumb'] : ($s2['subtitle1'] ?? '')));
        $heading = trim((string) (($s2['heading'] ?? '') !== '' ? $s2['heading'] : ($s2['title1'] ?? '')));
        $desc = trim((string) (($s2['description'] ?? '') !== '' ? $s2['description'] : ($s2['subtitle2'] ?? '')));
        $listRaw = trim((string) (($s2['bullet_points'] ?? '') !== '' ? $s2['bullet_points'] : (($s2['title2'] ?? '') !== '' ? $s2['title2'] : ($s2['subtitle3'] ?? ''))));
        $ctaLabel = trim((string) (($s2['button_label'] ?? '') !== '' ? $s2['button_label'] : ($s2['title3'] ?? '')));
        $ctaUrl = trim((string) (($s2['button_url'] ?? '') !== '' ? $s2['button_url'] : ($s2['url1'] ?? '')));

        if ($crumb === '') $crumb = __('Home / Collections /');
        if ($heading === '') $heading = __('Real Diamonds. Reimagined.');
        if ($desc === '') $desc = __('Lab-grown diamonds are chemically identical to mined diamonds, with the same brilliance and durability. The difference is intelligence, traceability, and modern craftsmanship.');
        if ($ctaLabel === '') $ctaLabel = __('Read the Diamond Guide');
        if ($ctaUrl === '') $ctaUrl = route('diamonds.index');

        $listItems = array_values(array_filter(array_map('trim', preg_split('/\s*\|\s*|\s*[\r\n]+\s*/', $listRaw) ?: [])));
        if (count($listItems) === 0) {
            $listItems = [
                __('IGI certified'),
                __('Exceptional cut and clarity'),
                __('Modern luxury choice'),
                __('Lower legacy inefficiency'),
            ];
        }

        $reimaginedHero = \App\Helpers\HomePageImageHelper::reimaginedBannerUrl();
    @endphp
    <section class="luxury-reimagined-banner">
        <div class="container">
            <div class="luxury-reimagined-banner__inner">
                <div class="luxury-reimagined-banner__copy">
                    <p class="luxury-reimagined-banner__crumb">{{ $crumb }}</p>
                    <h2>{{ $heading }}</h2>
                    <p class="luxury-reimagined-banner__desc">{{ $desc }}</p>
                    <ul class="luxury-reimagined-banner__list">
                        @foreach ($listItems as $li)
                            <li>{{ $li }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $ctaUrl }}" class="btn btn-luxury">{{ $ctaLabel }}</a>
                </div>
                <div class="luxury-reimagined-banner__media">
                    <img src="{{ $reimaginedHero }}" alt="{{ $heading }}" loading="lazy">
                </div>
            </div>
        </div>
    </section>
    @endif

    @if ($extra_settings->is_t3_popular_category == 1)
        <section class="newproduct-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3">{{ $popular_category_title }}</h2>

                        </div>
                        <div class="popular-category theme3">
                            <div class="links">
                                @foreach ($popular_categories as $key => $popular_categorie)
                                <a class="category_get {{$loop->first ? 'active' : ''}}" data-target="popular_category_view" data-href="{{route('front.popular.category',[$popular_categorie->slug,'popular_category','slider'])}}"  href="javascript:;" class="{{$loop->first ? 'active' : ''}}">{{$popular_categorie->name}}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="popular_category_view d-none">
                    <img  src="{{url('/core/public/storage/images/ajax_loader.gif')}}" alt="">
                </div>

                <div class="row" id="popular_category_view">
                    <div class="col-lg-12">
                        <div class="popular-category-slider  owl-carousel">
                            @foreach ($popular_category_items as $popular_category_item)
                            <div class="slider-item">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        @if (!$popular_category_item->is_stock())
                                            <div class="product-badge bg-secondary border-default text-body
                                            ">{{__('out of stock')}}</div>
                                        @endif
                                        @if($popular_category_item->previous_price && $popular_category_item->previous_price !=0)
                                        <div class="product-badge product-badge2 bg-info"> -{{PriceHelper::DiscountPercentage($popular_category_item)}}</div>
                                        @endif
                                            <img class="lazy" data-src="{{url('/core/public/storage/images/'.$popular_category_item->thumbnail)}}" alt="Product">
                                        <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$popular_category_item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>

                                        @include('includes.item_footer',['sitem' => $popular_category_item])
                                        </div>
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-category"><a href="{{route('front.catalog').'?category='.$popular_category_item->category->slug}}">{{$popular_category_item->category->name}}</a></div>
                                        <h3 class="product-title"><a href="{{route('front.product',$popular_category_item->slug)}}">
                                            {{ Str::limit($popular_category_item->name,35) }}
                                        </a></h3>
                                        <div class="rating-stars">
                                        <i class="fas fa-star filled"></i><i class="fas fa-star filled"></i><i class="fas fa-star filled"></i><i class="fas fa-star filled"></i><i class="fas fa-star filled"></i>
                                        </div>
                                        <h4 class="product-price">
                                            @if ($popular_category_item->previous_price != 0)
                                            <del>{{PriceHelper::setPreviousPrice($popular_category_item->previous_price)}}</del>
                                            @endif

                                            {{PriceHelper::grandCurrencyPrice($popular_category_item)}}
                                            </h4>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    @if ($extra_settings->is_t3_three_column_category == 1)
    <div class="flash-sell-area three_column_product mt-50">
        <div class="container">
            <div class="row gx-3 justify-content-center">
                @foreach ($two_column_categoriess as $two_column_key => $two_column_category)
                <div class="col-xl-4 col-lg-6">
                    <div class="section-title">
                        <h2 class="h3">{{ $two_column_category['name']->name }}</h2>
                    </div>
                    <div class="main-content">
                        <div class="newproduct-slider owl-carousel">
                            @foreach ($two_column_categoriess[$two_column_key]['items']->chunk(4) as $two_column_category_itemt)
                                <div class="slider-item">
                                    @foreach ($two_column_category_itemt as $two_column_category_item)
                                    <div class="product-card p-col">
                                        <a class="product-thumb" href="{{route('front.product',$two_column_category_item->slug)}}">
                                            @if(!$two_column_category_item->is_stock())
                                                <div class="product-badge bg-secondary border-default text-body
                                                ">{{__('out of stock')}}</div>
                                                @endif

                                            <img class="lazy" data-src="{{url('/core/public/storage/images/'.$two_column_category_item->thumbnail)}}" alt="Product"></a>
                                        <div class="product-card-body">
                                            <h3 class="product-title"><a href="{{route('front.product',$two_column_category_item->slug)}}">
                                                {{ Str::limit($two_column_category_item->name,40) }}
                                            </a></h3>
                                            <div class="rating-stars">
                                                {!! Helper::renderStarRating($two_column_category_item->reviews->avg('rating')) !!}
                                            </div>
                                            <h4 class="product-price">
                                            @if ($two_column_category_item->previous_price != 0)
                                            <del>{{PriceHelper::setPreviousPrice($two_column_category_item->previous_price)}}</del>
                                            @endif
                                                {{PriceHelper::grandCurrencyPrice($two_column_category_item)}}
                                            </h4>
                                        </div>
                                    </div>
                                    @endforeach

                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
    @endif

    @if ($extra_settings->is_t3_2_column_banner == 1)
   <div class="bannner-section mt-50" style="margin-bottom: 10px;">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-6">
                    <a href="{{$banner_third['url1']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::resolveFromAdmin($banner_third['img1'] ?? '', config('home_page_images.banner_third.1', [])) }}" alt="{{ $banner_third['title1'] ?? '' }}">
                        <div class="inner-content">
                            @if (isset($banner_third['subtitle1']))
                                <p>{{$banner_third['subtitle1']}}</p>
                            @endif
                            @if (isset($banner_third['title1']))
                                <h4>{{$banner_third['title1']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{$banner_third['url2']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::resolveFromAdmin($banner_third['img2'] ?? '', config('home_page_images.banner_third.2', [])) }}" alt="{{ $banner_third['title2'] ?? '' }}">
                        <div class="inner-content">
                            @if (isset($banner_third['subtitle2']))
                                <p>{{$banner_third['subtitle2']}} </p>
                            @endif
                            @if (isset($banner_third['title2']))
                                <h4>{{$banner_third['title2']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($extra_settings->is_t3_blog_section == 1)
        <div class="blog-section-h page_section mt-50 mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('Our Blog') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="home-blog-slider owl-carousel">
                            @foreach ($posts as $post)
                                <div class="slider-item">
                                    <a href="{{route('front.blog.details',$post->slug)}}" class="blog-post">
                                        <div class="post-thumb">
                                            <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::blogPostUrl($post->photo) }}"
                                                alt="{{ $post->title }}">
                                            </div>
                                        <div class="post-body">

                                            <h3 class="post-title"> {{ Str::limit($post->title, 55) }}
                                            </h3>
                                            <ul class="post-meta">

                                                <li><i class="icon-user"></i>{{ __('Admin') }}</li>
                                                <li><i class="icon-clock"></i>{{ date('jS F, Y', strtotime($post->created_at)) }}</li>
                                            </ul>
                                            <p>{{ Str::limit(strip_tags($post->content), 120, '...') }}
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($extra_settings->is_t3_brand_section == 1)
        <section class="brand-section mt-30 mb-60">
            <div class="container ">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('Popular Brands') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brand-slider owl-carousel">
                            @foreach ($brands as $brand)
                            <div class="slider-item">
                                <a class="text-center" href="{{ route('front.catalog') . '?brand=' . $brand->slug }}">
                                    <img class="d-block hi-50 lazy"
                                    data-src="{{ \App\Helpers\HomePageImageHelper::brandUrl($brand->photo) }}"
                                        alt="{{ $brand->name }}" title="{{ $brand->name }}">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif


@endsection

