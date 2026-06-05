@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')

    @php
        $hasHomeHeroVideo = is_file(public_path('storage/images/African_Carat_Ads_3.mp4'));
    @endphp
    @if ($setting->is_slider == 1 || $hasHomeHeroVideo)
        @include('components.home-luxury-hero')
    @endif


    @if ($setting->is_service == 1)
        <section class="service-section">
            <div class="container">
                <div class="row">
                    @foreach ($services as $service)
                        <div class="col-lg-3 col-sm-6 text-center mb-30">
                            <div class="single-service single-service2">
                                <img src="{{ url('/core/public/storage/images/' . $service->photo) }}" alt="Shipping">
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


    @if (false && $setting->campaign_status == 1)
        <div class="deal-of-day-section mt-20">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ $setting->campaign_title }}</h2>
                            <div class="right-area">
                                <div class="countdown countdown-alt" data-date-time="{{ $setting->campaign_end_date }}">
                                </div>
                                <a class="right_link" href="{{ route('front.campaign') }}">{{ __('View All') }} <i
                                        class="icon-chevron-right"></i></a>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">

                    <div class="col-lg-12">
                        <div class="popular-category-slider owl-carousel">
                            @foreach ($campaign_items as $compaign_item)
                                <div class="slider-item">
                                    <div class="product-card">
                                        <div class="product-thumb">
                                            @if (!$compaign_item->item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                        ">
                                                    {{ __('out of stock') }}</div>
                                            @endif

                                            @if ($compaign_item->item->previous_price && $compaign_item->item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($compaign_item->item) }}</div>
                                            @endif
                                            <img class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . $compaign_item->item->thumbnail) }}"
                                                alt="Product">
                                            <div class="product-button-group"><a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $compaign_item->item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                @if ($compaign_item->item->is_stock())
                                                    <a class="product-button add_to_single_cart"
                                                        data-target="{{ $compaign_item->item->id }}" href="javascript:;"
                                                        title="{{ __('To Cart') }}"><i class="icon-shopping-cart"></i>
                                                    </a>
                                                @else
                                                    <a class="product-button"
                                                        href="{{ route('front.product', $compaign_item->item->slug) }}"
                                                        title="{{ __('Details') }}"><i class="icon-arrow-right"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="product-card-body">

                                            <div class="product-category"><a
                                                    href="{{ route('front.catalog') . '?category=' . $compaign_item->item->category->slug }}">{{ $compaign_item->item->category->name }}</a>
                                            </div>
                                            <h3 class="product-title"><a
                                                    href="{{ route('front.product', $compaign_item->item->slug) }}">
                                                    {{ Str::limit($compaign_item->item->name, 35) }}
                                                </a></h3>
                                            <div class="rating-stars">
                                                {!! Helper::renderStarRating($compaign_item->item->reviews->avg('rating')) !!}
                                            </div>
                                            <h4 class="product-price">
                                                @if ($compaign_item->item->previous_price != 0)
                                                    <del>{{ PriceHelper::setPreviousPrice($compaign_item->item->previous_price) }}</del>
                                                @endif

                                                {{ PriceHelper::grandCurrencyPrice($compaign_item->item) }}
                                            </h4>

                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            </div>
        </div>
    @endif


    @if ($setting->is_three_c_b_first == 1)
        @include('front.partials.home-first-banner-strip', ['banner_first' => $banner_first])
    @endif


    @if ($setting->is_popular_category == 1)
        <section class="newproduct-section popular-category-sec mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ $popular_category_title }}</h2>
                            <div class="links">
                                @foreach ($popular_categories as $key => $popular_categorie)
                                    <a class="category_get {{ $loop->first ? 'active' : '' }}"
                                        data-target="popular_category_view"
                                        data-href="{{ route('front.popular.category', [$popular_categorie->slug, 'popular_category', 'slider']) }}"
                                        href="javascript:;"
                                        class="{{ $loop->first ? 'active' : '' }}">{{ $popular_categorie->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="popular_category_view d-none">
                    <img src="{{ url('/core/public/storage/images/ajax_loader.gif') }}" alt="">
                </div>

                <div class="row" id="popular_category_view">
                    <div class="col-lg-12">
                        <div class="popular-category-slider  owl-carousel">
                            @foreach ($popular_category_items as $popular_category_item)
                                <div class="slider-item">
                                    <div class="product-card">
                                        <div class="product-thumb">

                                            @if (!$popular_category_item->is_stock())
                                                <div
                                                    class="product-badge bg-secondary border-default text-body
                                            ">
                                                    {{ __('out of stock') }}</div>
                                            @endif
                                            @if ($popular_category_item->previous_price && $popular_category_item->previous_price != 0)
                                                <div class="product-badge product-badge2 bg-info">
                                                    -{{ PriceHelper::DiscountPercentage($popular_category_item) }}</div>
                                            @endif
                                            <img class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . $popular_category_item->thumbnail) }}"
                                                alt="Product">
                                            <div class="product-button-group">
                                                <a class="product-button wishlist_store"
                                                    href="{{ route('user.wishlist.store', $popular_category_item->id) }}"
                                                    title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                @include('includes.item_footer', [
                                                    'sitem' => $popular_category_item,
                                                ])
                                            </div>
                                        </div>
                                        <div class="product-card-body">
                                            <div class="product-category"><a
                                                    href="{{ route('front.catalog') . '?category=' . $popular_category_item->category->slug }}">{{ $popular_category_item->category->name }}</a>
                                            </div>
                                            <h3 class="product-title"><a
                                                    href="{{ route('front.product', $popular_category_item->slug) }}">
                                                    {{ Str::limit($popular_category_item->name, 35) }}
                                                </a></h3>
                                            <div class="rating-stars">
                                                {!! Helper::renderStarRating($popular_category_item->reviews->avg('rating')) !!}
                                            </div>
                                            <h4 class="product-price">
                                                @if ($popular_category_item->previous_price != 0)
                                                    <del>{{ PriceHelper::setPreviousPrice($popular_category_item->previous_price) }}</del>
                                                @endif
                                                {{ PriceHelper::grandCurrencyPrice($popular_category_item) }}
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

    @if ($setting->is_three_c_b_second == 1)
        <div class="bannner-section mt-60">
            <div class="container ">
                <div class="row gx-3">
                    <div class="col-md-4">
                        <a href="{{ $banner_secend['url1'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_secend['img1']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_secend['subtitle1']))
                                    <p>{{ $banner_secend['subtitle1'] }}</p>
                                @endif

                                @if (isset($banner_secend['title1']))
                                    <h4>{{ $banner_secend['title1'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ $banner_secend['url2'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_secend['img2']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_secend['subtitle2']))
                                    <p>{{ $banner_secend['subtitle2'] }}</p>
                                @endif

                                @if (isset($banner_secend['title2']))
                                    <h4> {{ $banner_secend['title2'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ $banner_secend['url3'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_secend['img3']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_secend['subtitle3']))
                                    <p>{{ $banner_secend['subtitle3'] }} </p>
                                @endif

                                @if (isset($banner_secend['title3']))
                                    <h4>{{ $banner_secend['title3'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($setting->is_highlighted == 1)
        <section class="selected-product-section speacial-product-sec mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <div class="links">
                                <a data-href="{{ route('front.get.product', 'feature') }}" data-target="type_product_view"
                                    href="javascript:;" class="product_get active">{{ __('Featured') }}</a>
                                <a data-href="{{ route('front.get.product', 'best') }}" data-target="type_product_view"
                                    class="product_get" href="javascript:;">{{ __('Best Seller') }}</a>
                                <a data-href="{{ route('front.get.product', 'top') }}" data-target="type_product_view"
                                    class="product_get" href="javascript:;">{{ __('Top Rated') }}</a>
                                <a data-href="{{ route('front.get.product', 'new') }}" data-target="type_product_view"
                                    class="product_get" href="javascript:;">{{ __('New Product') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="type_product_view d-none">
                        <img src="{{ url('/core/public/storage/images/ajax_loader.gif') }}" alt="">
                    </div>
                    <div class="col-lg-12" id="type_product_view">

                        <div class="features-slider  owl-carousel">
                            @foreach ($products->orderBy('id', 'DESC')->get() as $item)
                                @if ($item->is_type == 'feature')
                                    <div class="slider-item">
                                        <div class="product-card ">
                                            <div class="product-thumb">
                                                @if (!$item->is_stock())
                                                    <div
                                                        class="product-badge bg-secondary border-default text-body
                                                    ">
                                                        {{ __('out of stock') }}</div>
                                                @endif
                                                @if ($item->previous_price && $item->previous_price != 0)
                                                    <div class="product-badge product-badge2 bg-info">
                                                        -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                @endif
                                                <img class="lazy"
                                                    data-src="{{ url('/core/public/storage/images/' . $item->thumbnail) }}"
                                                    alt="Product">
                                                <div class="product-button-group"><a class="product-button wishlist_store"
                                                        href="{{ route('user.wishlist.store', $item->id) }}"
                                                        title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                    @include('includes.item_footer', ['sitem' => $item])
                                                </div>
                                            </div>
                                            <div class="product-card-inner">
                                                <div class="product-card-body">
                                                    <div class="product-category"><a
                                                            href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                    </div>
                                                    <h3 class="product-title"><a
                                                            href="{{ route('front.product', $item->slug) }}">
                                                            {{ Str::limit($item->name, 35) }}
                                                        </a></h3>
                                                    <div class="rating-stars">
                                                        {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                    </div>
                                                    <h4 class="product-price">
                                                        @if ($item->previous_price != 0)
                                                            <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                        @endif
                                                        {{ PriceHelper::grandCurrencyPrice($item) }}
                                                    </h4>
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
        </section>
    @endif

    @if ($extra_settings->is_t1_falsh == 1)
        <div class="flash-sell-new-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Flash Deal') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-content">
                            <div class="flash-deal-slider owl-carousel">
                                @foreach ($products->orderBy('id', 'DESC')->get() as $item)
                                    @if ($item->is_type == 'flash_deal' && $item->date != null)
                                        <div class="slider-item">
                                            <div class="product-card ">
                                                <div class="product-thumb">
                                                    @if (!$item->is_stock())
                                                        <div
                                                            class="product-badge bg-secondary border-default text-body
                                            ">
                                                            {{ __('out of stock') }}</div>
                                                    @endif
                                                    @if ($item->previous_price && $item->previous_price != 0)
                                                        <div class="product-badge product-badge2 bg-info">
                                                            -{{ PriceHelper::DiscountPercentage($item) }}</div>
                                                    @endif
                                                    <img class="lazy"
                                                        data-src="{{ url('/core/public/storage/images/' . $item->thumbnail) }}"
                                                        alt="Product">
                                                    <div class="product-button-group"><a
                                                            class="product-button wishlist_store"
                                                            href="{{ route('user.wishlist.store', $item->id) }}"
                                                            title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                                        @include('includes.item_footer', [
                                                            'sitem' => $item,
                                                        ])
                                                    </div>
                                                </div>
                                                <div class="product-card-inner">
                                                    <div class="product-card-body">

                                                        <div class="product-category"><a
                                                                href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                                        </div>
                                                        <h3 class="product-title"><a
                                                                href="{{ route('front.product', $item->slug) }}">
                                                                {{ Str::limit($item->name, 50) }}
                                                            </a></h3>
                                                        <div class="rating-stars">
                                                            {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                                        </div>
                                                        <h4 class="product-price">
                                                            @if ($item->previous_price != 0)
                                                                <del>{{ PriceHelper::setPreviousPrice($item->previous_price) }}</del>
                                                            @endif

                                                            {{ PriceHelper::grandCurrencyPrice($item) }}
                                                        </h4>
                                                        @if (date('d-m-y') != \Carbon\Carbon::parse($item->date)->format('d-m-y'))
                                                            <div class="countdown countdown-alt mb-3"
                                                                data-date-time="{{ $item->date }}">
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

    @if ($setting->is_two_column_category == 1)
        <div class="flash-sell-area mt-50">
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
                                                    <a class="product-thumb"
                                                        href="{{ route('front.product', $two_column_category_item->slug) }}">
                                                        @if (!$two_column_category_item->is_stock())
                                                            <div
                                                                class="product-badge bg-secondary border-default text-body
                                                    ">
                                                                {{ __('out of stock') }}</div>
                                                        @endif

                                                        <img class="lazy"
                                                            data-src="{{ url('/core/public/storage/images/' . $two_column_category_item->thumbnail) }}"
                                                            alt="Product">
                                                    </a>
                                                    <div class="product-card-body">
                                                        <h3 class="product-title"><a
                                                                href="{{ route('front.product', $two_column_category_item->slug) }}">
                                                                {{ Str::limit($two_column_category_item->name, 40) }}
                                                            </a></h3>
                                                        <div class="rating-stars">
                                                            {!! Helper::renderStarRating($two_column_category_item->reviews->avg('rating')) !!}
                                                        </div>
                                                        <h4 class="product-price">
                                                            @if ($two_column_category_item->previous_price != 0)
                                                                <del>{{ PriceHelper::setPreviousPrice($two_column_category_item->previous_price) }}</del>
                                                            @endif
                                                            {{ PriceHelper::grandCurrencyPrice($two_column_category_item) }}
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

    @if ($setting->is_two_c_b == 1)
        <div class="bannner-section mt-50">
            <div class="container ">
                <div class="row gx-3">
                    <div class="col-md-6">
                        <a href="{{ $banner_third['url1'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_third['img1']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_third['subtitle1']))
                                    <p>{{ $banner_third['subtitle1'] }}</p>
                                @endif
                                @if (isset($banner_third['title1']))
                                    <h4>{{ $banner_third['title1'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ $banner_third['url2'] }}" class="genius-banner">
                            <img class="lazy" data-src="{{ url('/core/public/storage/images/' . $banner_third['img2']) }}"
                                alt="">
                            <div class="inner-content">
                                @if (isset($banner_third['subtitle2']))
                                    <p>{{ $banner_third['subtitle2'] }} </p>
                                @endif
                                @if (isset($banner_third['title2']))
                                    <h4>{{ $banner_third['title2'] }}</h4>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($setting->is_featured_category == 1)
        <section class="selected-product-section featured_cat_sec sps-two mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ $feature_category_title }}</h2>
                            <div class="links">
                                @foreach ($feature_categories as $key => $feature_category)
                                    <a class="category_get {{ $loop->first ? 'active' : '' }}"
                                        data-target="feature_category_view"
                                        data-href="{{ route('front.popular.category', [$feature_category->slug, 'feature_category', 'normal']) }}"
                                        href="javascript:;"
                                        class="{{ $loop->first ? 'active' : '' }}">{{ $feature_category->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="feature_category_view d-none">
                    <img src="{{ url('/core/public/storage/images/ajax_loader.gif') }}" alt="">
                </div>
                <div class="row g-3" id="feature_category_view">
                    @foreach ($feature_category_items as $feature_category_item)
                        <div class="col-gd">
                            <div class="product-card">
                                <div class="product-thumb">

                                    @if (!$feature_category_item->is_stock())
                                        <div
                                            class="product-badge bg-secondary border-default text-body
                                    ">
                                            {{ __('out of stock') }}</div>
                                    @endif
                                    @if ($feature_category_item->previous_price && $feature_category_item->previous_price != 0)
                                        <div class="product-badge product-badge2 bg-info">
                                            -{{ PriceHelper::DiscountPercentage($feature_category_item) }}</div>
                                    @endif
                                    <img class="lazy"
                                        data-src="{{ url('/core/public/storage/images/' . $feature_category_item->thumbnail) }}"
                                        alt="Product">
                                    <div class="product-button-group"><a class="product-button wishlist_store"
                                            href="{{ route('user.wishlist.store', $feature_category_item->id) }}"
                                            title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                        @include('includes.item_footer', [
                                            'sitem' => $feature_category_item,
                                        ])

                                    </div>
                                </div>
                                <div class="product-card-body">
                                    <div class="product-category"><a
                                            href="{{ route('front.catalog') . '?category=' . $feature_category_item->category->slug }}">{{ $feature_category_item->category->name }}</a>
                                    </div>
                                    <h3 class="product-title"><a
                                            href="{{ route('front.product', $feature_category_item->slug) }}">
                                            {{ Str::limit($feature_category_item->name, 35) }}
                                        </a></h3>
                                    <div class="rating-stars">
                                        {!! Helper::renderStarRating($feature_category_item->reviews->avg('rating')) !!}
                                    </div>
                                    <h4 class="product-price">
                                        @if ($feature_category_item->previous_price != 0)
                                            <del>{{ PriceHelper::setPreviousPrice($feature_category_item->previous_price) }}</del>
                                        @endif
                                        {{ PriceHelper::grandCurrencyPrice($feature_category_item) }}
                                    </h4>
                                </div>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>
    @endif

    @if ($setting->is_blogs == 1)
        <div class="blog-section-h page_section mt-50 mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Our Blog') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="home-blog-slider owl-carousel">
                            @foreach ($posts as $post)
                                <div class="slider-item">
                                    <a href="{{ route('front.blog.details', $post->slug) }}" class="blog-post">
                                        <div class="post-thumb">
                                            <img class="lazy"
                                                data-src="{{ url('/core/public/storage/images/' . json_decode($post->photo, true)[array_key_first(json_decode($post->photo, true))]) }}"
                                                alt="Blog Post">
                                        </div>
                                        <div class="post-body">

                                            <h3 class="post-title"> {{ Str::limit($post->title, 55) }}
                                            </h3>
                                            <ul class="post-meta">

                                                <li><i class="icon-user"></i>{{ __('Admin') }}</li>
                                                <li><i
                                                        class="icon-clock"></i>{{ date('jS F, Y', strtotime($post->created_at)) }}
                                                </li>
                                            </ul>
                                            <p>{{ Str::limit(strip_tags($post->details), 120) }}
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

    @if ($setting->is_popular_brand == 1)
        <section class="brand-section mt-30 mb-60">
            <div class="container ">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title">
                            <h2 class="h3">{{ __('Popular Brands') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="brand-slider owl-carousel">
                            @foreach ($brands as $brand)
                                <div class="slider-item">
                                    <a class="text-center"
                                        href="{{ route('front.catalog') . '?brand=' . $brand->slug }}">
                                        <img class="d-block hi-50 lazy"
                                            data-src="{{ url('/core/public/storage/images/' . $brand->photo) }}"
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
