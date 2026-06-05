@extends('master.front')
@section('meta')
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <meta name="description" content="{{ $setting->meta_description }}">
@endsection

@section('content')

 

    @php
        $hasHomeHeroVideo = is_file(public_path('storage/images/African_Carat_Ads_3.mp4'));
    @endphp
    @if ($extra_settings->is_t4_slider == 1 || $hasHomeHeroVideo)
        @include('components.home-luxury-hero')
    @endif

    @if ($extra_settings->is_t4_featured_banner == 1)
        <div class="featured-for-home3 mt-60">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <a href="{{isset($home_page4_banner['url1']) ? $home_page4_banner['url1'] : ''}}" class="h3-category">
                            <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::theme4FeaturedUrl($home_page4_banner['img1'] ?? null, 1) }}" alt="{{ $home_page4_banner['label1'] ?? '' }}">
                            <h4>{{isset($home_page4_banner['label1']) ? $home_page4_banner['label1'] : ''}}</h4>
                        </a>
                        <a href="{{isset($home_page4_banner['url2']) ? $home_page4_banner['url2'] : ''}}" class="h3-category">
                            <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::theme4FeaturedUrl($home_page4_banner['img2'] ?? null, 2) }}" alt="{{ $home_page4_banner['label2'] ?? '' }}">
                            <h4>{{isset($home_page4_banner['label2']) ? $home_page4_banner['label2'] : ''}}</h4>
                        </a>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 middleImage">
                        <a href="{{isset($home_page4_banner['url3']) ? $home_page4_banner['url3'] : ''}}" class="h3-category">
                            <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::theme4FeaturedUrl($home_page4_banner['img3'] ?? null, 3) }}" alt="{{ $home_page4_banner['label3'] ?? '' }}">
                            <h4>{{isset($home_page4_banner['label3']) ? $home_page4_banner['label3'] : ''}}</h4>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <a href="{{isset($home_page4_banner['url4']) ? $home_page4_banner['url4'] : ''}}" class="h3-category">
                            <img src="{{ \App\Helpers\HomePageImageHelper::theme4FeaturedUrl($home_page4_banner['img4'] ?? null, 4) }}" alt="{{ $home_page4_banner['label4'] ?? '' }}">
                            <h4>{{isset($home_page4_banner['label4']) ? $home_page4_banner['label4'] : ''}}</h4>
                        </a>
                        <a href="{{isset($home_page4_banner['url5']) ? $home_page4_banner['url5'] : ''}}" class="h3-category">
                            <img src="{{ \App\Helpers\HomePageImageHelper::theme4FeaturedUrl($home_page4_banner['img5'] ?? null, 5) }}" alt="{{ $home_page4_banner['label5'] ?? '' }}">
                            <h4>{{isset($home_page4_banner['label5']) ? $home_page4_banner['label5'] : ''}}</h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if ($extra_settings->is_t4_specialpick == 1)
        <section class="selected-product-section mt-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title section-title2 section-title3">
                            <h2 class="h3">{{ __('Special Pick') }}</h2>
                        </div>
                        <div class="popular-category theme3">
                            <div class="links">
                                <a data-href="{{route('front.get.product','feature')}}" data-target="type_product_view" href="javascript:;" class="product_get active">{{__('Featured')}}</a>
                                <a data-href="{{route('front.get.product','best')}}" data-target="type_product_view" class="product_get" href="javascript:;">{{__('Best Seller')}}</a>
                                <a data-href="{{route('front.get.product','top')}}" data-target="type_product_view" class="product_get" href="javascript:;">{{__('Top Rated')}}</a>
                                <a data-href="{{route('front.get.product','new')}}" data-target="type_product_view" class="product_get" href="javascript:;">{{__('New Product')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="type_product_view d-none">
                        <img  src="{{url('/core/public/storage/images/ajax_loader.gif')}}" alt="">
                    </div>
                    <div class="col-lg-12" id="type_product_view">

                        <div class="features-slider  owl-carousel" >
                            @foreach ($products->orderBy('id','DESC')->get()  as $item)
                                @if ($item->is_type == 'feature')
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
                                                    {{ Str::limit($item->name,35) }}
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

    @if ($extra_settings->is_t4_3_column_banner_first == 1)
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

    @include('front.partials.home-trust-band')

    @if ($extra_settings->is_t4_flashdeal == 1)
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

    @if ($extra_settings->is_t4_3_column_banner_second == 1)
    <div class="bannner-section mt-60">
        <div class="container ">
            <div class="row gx-3">
                <div class="col-md-4">
                    <a href="{{$banner_secend['url1']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::resolveFromAdmin($banner_secend['img1'] ?? '', config('home_page_images.banner_second_row.1', [])) }}" alt="{{ $banner_secend['title1'] ?? '' }}">
                        <div class="inner-content">
                            @if (isset($banner_secend['subtitle1']))
                                <p>{{$banner_secend['subtitle1']}}</p>
                            @endif

                            @if (isset($banner_secend['title1']))
                                <h4>{{$banner_secend['title1']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{$banner_secend['url2']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::resolveFromAdmin($banner_secend['img2'] ?? '', config('home_page_images.banner_second_row.2', [])) }}" alt="{{ $banner_secend['title2'] ?? '' }}">
                        <div class="inner-content">
                            @if (isset($banner_secend['subtitle2']))
                                <p>{{$banner_secend['subtitle2']}}</p>
                            @endif

                            @if (isset($banner_secend['title2']))
                                <h4> {{$banner_secend['title2']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{$banner_secend['url3']}}" class="genius-banner">
                        <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::resolveFromAdmin($banner_secend['img3'] ?? '', config('home_page_images.banner_second_row.3', [])) }}" alt="{{ $banner_secend['title3'] ?? '' }}">
                        <div class="inner-content">
                            @if (isset($banner_secend['subtitle3']))
                                <p>{{$banner_secend['subtitle3']}} </p>
                            @endif

                            @if (isset($banner_secend['title3']))
                                <h4>{{$banner_secend['title3']}}</h4>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($extra_settings->is_t4_popular_category == 1)
        @if (count($pupular_cateogry_home4)>0)
            @foreach ($pupular_cateogry_home4 as $popularcategory)

            <div class="flash-sell-area theme2 mt-50">
                <div class="container">
                    <div class="row">
                        <div class="col-xxl-12">
                            <div class="section-title section-title2 section-title3">
                                <h2 class="h3">{{$popularcategory->name}}</h2>
                            </div>
                            <div class="main-content">
                                <div class="features-slider  owl-carousel" >
                                    @foreach ($popularcategory->items  as $item)

                                        <div class="slider-item">
                                            <div class="product-card ">
                                                <div class="product-thumb" >
                                                    @if ($item->is_stock())
                                                    @if($item->is_type == 'new')
                                                    @else
                                                        <div class="product-badge
                                                            @if($item->is_type == 'feature')
                                                            bg-warning
                                                            @elseif($item->is_type == 'new')

                                                            @elseif($item->is_type == 'top')
                                                            bg-info
                                                            @elseif($item->is_type == 'best')
                                                            bg-dark
                                                            @elseif($item->is_type == 'flash_deal')
                                                            bg-success
                                                            @endif
                                                            "> {{   ucfirst(str_replace('_',' ',$item->is_type))   }}
                                                        </div>
                                                    @endif
                                                    @else
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
                                                        {{ Str::limit($item->name,35) }}
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

                                                </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    @endif

    @if ($extra_settings->is_t4_2_column_banner == 1)
    <div class="bannner-section mt-50">
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

    @if ($extra_settings->is_t4_blog_section == 1)
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
                                                alt="Blog Post">
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

    @if ($extra_settings->is_t4_brand_section == 1)
        <section class="brand-section mt-30">
            <div class="container ">
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="section-title  section-title2 section-title3">
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

    @if ($extra_settings->is_t4_service_section == 1)
        <section class="service-section mt-50 pt-0 mb-30">
            <div class="container">
                <div class="row">
                    @foreach ($services as $service)
                        <div class="col-lg-3 col-sm-6 text-center mb-30">
                            <div class="single-service single-service2">
                                <img class="lazy" data-src="{{ \App\Helpers\HomePageImageHelper::serviceUrl($service->photo, $loop->index) }}" alt="{{ $service->title }}">
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

@endsection

