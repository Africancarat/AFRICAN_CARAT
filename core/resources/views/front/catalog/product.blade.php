@extends('master.front')

@section('title')
    {{ $item->name }}
@endsection


@section('meta')
    <meta name="tile" content="{{ $item->title }}">
    <meta name="keywords" content="{{ $item->meta_keywords }}">
    <meta name="description" content="{{ $item->meta_description }}">

    <meta name="twitter:title" content="{{ $item->title }}">
    <meta name="twitter:image" content="{{ \App\Helpers\ImageHelper::storageImageUrl($item->photo) }}">
    <meta name="twitter:description" content="{{ $item->meta_description }}">

    <meta name="og:title" content="{{ $item->title }}">
    <meta name="og:image" content="{{ \App\Helpers\ImageHelper::storageImageUrl($item->photo) }}">
    <meta name="og:description" content="{{ $item->meta_description }}">
@endsection



@section('content')
    <div class="page-title">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumbs">
                        <li><a href="{{ route('front.index') }}">{{ __('Home') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li><a href="{{ route('front.catalog') }}">{{ __('Shop') }}</a>
                        </li>
                        <li class="separator"></li>
                        <li>{{ $item->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Content-->
    <div class="container padding-bottom-1x mb-1">
        <div class="row">
            <!-- Poduct Gallery-->
            <div class="col-xxl-6 col-lg-6 col-md-6">
                <div class="product-gallery">
                    @php
                        $daTop = $item->diamondAttribute;
                        $hasDiamond360Top = $daTop && (filled($daTop->video_360_url ?? null)
                            || (is_array($daTop->images_360 ?? null) && count($daTop->images_360) >= 2));
                    @endphp
                    @if ($item->video && ! $hasDiamond360Top)
                        <div class="gallery-wrapper">
                            <div class="gallery-item video-btn text-center">
                                <a href="{{ $item->video }}" title="Watch video"></a>
                            </div>
                        </div>
                    @endif
                    @if (! $item->is_stock())
                        <span class="product-badge bg-secondary border-default text-body">{{ __('out of stock') }}</span>
                    @endif

                    @include('front.catalog.partials.pdp-gallery-column', [
                        'item' => $item,
                        'pdp_slider_images' => $pdp_slider_images ?? [],
                        'pdp_primary_still' => $pdp_primary_still ?? null,
                        'pdp_metal_images' => $pdp_metal_images ?? [],
                        'pdp_default_metal' => $pdp_default_metal ?? \App\Services\JewelryPdpMediaService::DEFAULT_METAL_KEY,
                        'pdp_viewer_meta' => $pdp_viewer_meta ?? [],
                        'pdp_show_media_gallery' => $pdp_show_media_gallery ?? false,
                    ])
                </div>

                {{-- Desktop: use empty space under media for supporting blocks --}}
                <div class="d-none d-lg-block mt-3">
                    @include('front.catalog.partials.pdp-jewelry-post-cart', [
                        'item' => $item,
                        'show_pdp_engraving' => $show_pdp_engraving ?? false,
                        'show_emi_estimate' => $show_emi_estimate ?? false,
                        'show_drop_hint' => $show_drop_hint ?? false,
                        'financing_pdp' => $financing_pdp,
                    ])
                    @if ($item->diamondAttribute)
                        @include('front.catalog.partials.diamond-certificate-panel', ['item' => $item])
                    @endif
                </div>
            </div>
            <!-- Product Info-->
            <div class="col-xxl-6 col-lg-6 col-md-6">
                <div class="details-page-top-right-content d-flex align-items-start">
                    <div class="div w-100">
                        <input type="hidden" id="item_id" value="{{ $item->id }}">
                        @php
                            $pdpLineUnitBase = (float) ($pdp_line_unit_base ?? $item->discount_price);
                        @endphp
                        <input type="hidden" id="demo_price"
                            value="{{ PriceHelper::setConvertPrice($pdpLineUnitBase) }}">
                        {{-- Base unit price (same units as items.discount_price); jewelry tiers may update via pdp-jewelry-extras --}}
                        <input type="hidden" id="pdp_line_base_price" value="{{ $pdpLineUnitBase }}">
                        <input type="hidden" value="{{ PriceHelper::setCurrencySign() }}" id="set_currency">
                        <input type="hidden" value="{{ PriceHelper::setCurrencyValue() }}" id="set_currency_val">
                        <input type="hidden" value="{{ $setting->currency_direction }}" id="currency_direction">
                        @php
                            $pdpOnWishlist = Auth::check()
                                && App\Models\Wishlist::where('user_id', Auth::user()->id)
                                    ->where('item_id', $item->id)
                                    ->exists();
                        @endphp
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                            <h4 class="mb-0 p-title-main flex-grow-1 pe-2">{{ $item->name }}</h4>
                            <a class="wishlist_store pdp-title-wishlist {{ $pdpOnWishlist ? 'is-in-wishlist' : '' }}"
                                href="{{ route('user.wishlist.store', $item->id) }}"
                                data-label-add="{{ __('Add to wishlist') }}"
                                data-label-added="{{ __('In your wishlist') }}"
                                title="{{ $pdpOnWishlist ? __('In your wishlist') : __('Add to wishlist') }}"
                                aria-label="{{ $pdpOnWishlist ? __('In your wishlist') : __('Add to wishlist') }}">
                                <i class="icon-heart" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="mb-3">
                            <div class="rating-stars d-inline-block gmr-3">
                                {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                            </div>
                            @if ($item->is_stock())
                                <span class="text-success  d-inline-block">{{ __('In Stock') }} <b>({{ $item->stock }}
                                        @lang('items'))</b></span>
                            @else
                                <span class="text-danger  d-inline-block">{{ __('Out of stock') }}</span>
                            @endif
                        </div>


                        <div id="add-to-cart" class="pdp-buy-box">
                        <p class="text-muted">{{ $item->sort_details }} <a href="#details"
                                class="scroll-to">{{ __('Read more') }}</a></p>

                        @if ($item->diamondAttribute)
                            @php $dq = $item->diamondAttribute; @endphp
                            <div class="diamond-quick-specs mb-3 small">
                                <div class="d-flex flex-wrap gap-3 text-uppercase letter-spacing"
                                    style="letter-spacing:0.08em;font-size:10px;">
                                    @if($dq->shape)<span><strong>{{ __('Shape') }}</strong> {{ $dq->shape }}</span>@endif
                                    @if($dq->carat_weight)<span><strong>{{ __('Carat') }}</strong> {{ $dq->carat_weight }} ct</span>@endif
                                    @if($dq->cut_grade)<span><strong>{{ __('Cut') }}</strong> {{ $dq->cut_grade }}</span>@endif
{{--                                    @php--}}
{{--                                        $dqColor = is_array($dq->color_grade ?? null) ? implode(', ', $dq->color_grade) : ($dq->color_grade ?? null);--}}
{{--                                        $dqClarity = is_array($dq->clarity_grade ?? null) ? implode(', ', $dq->clarity_grade) : ($dq->clarity_grade ?? null);--}}
{{--                                    @endphp--}}
{{--                                    @if($dqColor)<span><strong>{{ __('Colour') }}</strong> {{ $dqColor }}</span>@endif--}}
{{--                                    @if($dqClarity)<span><strong>{{ __('Clarity') }}</strong> {{ $dqClarity }}</span>@endif--}}
                                </div>
                            </div>

                        @endif

                        @include('front.catalog.partials.pdp-jewelry-extras', [
                            'item' => $item,
                            'pdp_line_unit_base' => $pdpLineUnitBase,
                            'show_pdp_engraving' => $show_pdp_engraving ?? false,
                            'show_emi_estimate' => $show_emi_estimate ?? false,
                            'show_drop_hint' => $show_drop_hint ?? false,
                            'financing_pdp' => $financing_pdp,
                        ])

                        <div class="row margin-top-1x">
                            @foreach ($attributes as $attribute)
                                @if ($attribute->options->count() != 0)
                                    @if(stripos($attribute->name, 'size') !== false)
                                        <span id="pdpRingSizeAnchor" class="d-block"></span>
                                        {{-- Luxury ring size selector --}}
                                        <div class="col-sm-12">
                                            <div class="ring-size-selector">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="filter-label">{{ $attribute->name }}</label>
                                                    <button type="button" class="size-guide-link" data-bs-toggle="modal"
                                                        data-bs-target="#ringSizerModal">
                                                        {{ __("Not sure? Use our ring sizer") }} &rsaquo;
                                                    </button>
                                                </div>
                                                <div class="size-grid">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <button type="button" class="size-btn"
                                                            data-type="{{ $attribute->id }}"
                                                            data-href="{{ $option->id }}"
                                                            data-target="{{ PriceHelper::setConvertPrice($option->price) }}"
                                                            data-select-id="{{ $attribute->name }}"
                                                            data-value="{{ $option->name }}"
                                                            onclick="selectRingSize(this)">
                                                            {{ $option->name }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                                <div class="mt-2">
                                                    <label class="small text-muted mb-1 d-block">{{ __('Or enter custom size') }}</label>
                                                    <input type="text"
                                                        class="form-control form-control-sm"
                                                        id="custom_ring_size_{{ $attribute->id }}"
                                                        placeholder="{{ __('e.g. 6, 7, 8') }}"
                                                        inputmode="decimal"
                                                        autocomplete="off"
                                                        oninput="syncCustomRingSize(this, @json($attribute->name))">
                                                    <small class="text-muted d-block mt-1">
                                                        <a href="#" class="text-decoration-none"
                                                           data-bs-toggle="modal" data-bs-target="#ringSizerModal">{{ __('Find your ring size') }}</a>
                                                    </small>
                                                </div>
                                                {{-- Hidden select keeps existing cart JS working --}}
                                                <select class="form-control attribute_option d-none" id="{{ $attribute->name }}">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <option value="{{ $option->name }}" data-type="{{ $attribute->id }}"
                                                            data-href="{{ $option->id }}"
                                                            data-target="{{ PriceHelper::setConvertPrice($option->price) }}">
                                                            {{ $option->name }}</option>
                                                    @endforeach
                                                </select>
                                                <p class="resize-note">{{ __('Complimentary resizing within 60 days of delivery on eligible styles.') }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="{{ $attribute->name }}">{{ $attribute->name }}</label>
                                                <select class="form-control attribute_option" id="{{ $attribute->name }}">
                                                    @foreach ($attribute->options->where('stock', '!=', '0') as $option)
                                                        <option value="{{ $option->name }}" data-type="{{ $attribute->id }}"
                                                            data-href="{{ $option->id }}"
                                                            data-target="{{ PriceHelper::setConvertPrice($option->price) }}">
                                                            {{ $option->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                        <div class="pb-4">
                            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                                @if ($item->item_type == 'normal')
                                    <div class="qtySelector product-quantity mb-2 mb-sm-0">
                                        <span class="decreaseQty subclick"><i class="fas fa-minus "></i></span>
                                        <input type="text" class="qtyValue cart-amount" value="1">
                                        <span class="increaseQty addclick"><i class="fas fa-plus"></i></span>
                                        <input type="hidden" value="3333" id="current_stock">
                                    </div>
                                @endif

                                <div class="p-action-button d-flex gap-3 flex-grow-1 justify-content-end flex-wrap">
                                    @if ($item->item_type != 'affiliate')
                                        @if ($item->is_stock())
                                            <button class="btn btn-primary m-0 a-t-c-mr" id="add_to_cart"><i
                                                    class="icon-bag"></i><span>{{ __('Add to Cart') }}</span></button>
                                            <button class="btn btn-primary m-0" id="but_to_cart"><i
                                                    class="icon-bag"></i><span>{{ __('Buy Now') }}</span></button>
                                        @else
                                            <button class="btn btn-primary m-0"><i
                                                    class="icon-bag"></i><span>{{ __('Out of stock') }}</span></button>
                                        @endif
                                    @else
                                        <a href="{{ $item->affiliate_link }}" target="_blank"
                                            class="btn btn-primary m-0"><span><i
                                                    class="icon-bag"></i>{{ __('Buy Now') }}</span></a>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 d-grid gap-3">
                                <a class="btn btn-dark text-uppercase text-white" href="{{ route('front.contact', ['reason' => 'customize-piece', 'item' => $item->id]) }}">
                                    {{ __('Customize this piece') }}
                                </a>
                                <a class="btn btn-secondary text-uppercase text-white" href="{{ route('front.contact') }}">
                                    {{ __('Book consultation') }}
                                </a>
                            </div>

                            <div class="mt-4 pdp-support-links">
                                <div class="d-flex justify-content-between text-center gap-3 flex-wrap">
                                    <div class="pdp-support-links__item">
                                        <div class="pdp-support-links__icon">💎</div>
                                        <div class="pdp-support-links__label">{{ __('IGI Certified') }}</div>
                                    </div>
                                    <div class="pdp-support-links__item">
                                        <div class="pdp-support-links__icon">🔒</div>
                                        <div class="pdp-support-links__label">{{ __('Secure Checkout') }}</div>
                                    </div>
                                    <div class="pdp-support-links__item">
                                        <div class="pdp-support-links__icon">🚚</div>
                                        <div class="pdp-support-links__label">{{ __('Insured Delivery') }}</div>
                                    </div>
                                    <div class="pdp-support-links__item">
                                        <div class="pdp-support-links__icon">🛠</div>
                                        <div class="pdp-support-links__label">{{ __('Lifetime Care Guidance') }}</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex flex-wrap gap-3 text-uppercase" style="letter-spacing:0.12em;font-size:11px;">
                                    <a href="#" class="text-decoration-none"
                                       data-bs-toggle="modal" data-bs-target="#ringSizerModal">{{ __('Size guide') }}</a>
                                    <span class="text-muted">|</span>
                                    <a href="{{ route('front.contact', ['reason' => 'shipping-delivery']) }}" class="text-decoration-none">{{ __('Shipping & delivery') }}</a>
                                    <span class="text-muted">|</span>
                                    <a href="{{ route('front.contact', ['reason' => 'returns']) }}" class="text-decoration-none">{{ __('Returns') }}</a>
                                </div>
                            </div>
                        </div>
                        {{-- Mobile: keep below Add to Cart (desktop is under the image) --}}
                        <div class="d-lg-none">
                            @include('front.catalog.partials.pdp-jewelry-post-cart', [
                                'item' => $item,
                                'show_pdp_engraving' => $show_pdp_engraving ?? false,
                                'show_emi_estimate' => $show_emi_estimate ?? false,
                                'show_drop_hint' => $show_drop_hint ?? false,
                                'financing_pdp' => $financing_pdp,
                            ])
                            @if ($item->diamondAttribute)
                                @include('front.catalog.partials.diamond-certificate-panel', ['item' => $item])
                            @endif
                        </div>
                        </div>{{-- end #add-to-cart buy box --}}

                        <div class="div">
                            <div class="t-c-b-area">
                                @if ($item->brand_id)
                                    <div class="pt-1 mb-1"><span class="text-medium">{{ __('Brand') }}:</span>
                                        <a
                                            href="{{ route('front.catalog') . '?brand=' . $item->brand->slug }}">{{ $item->brand->name }}</a>
                                    </div>
                                @endif

                                <div class="pt-1 mb-1"><span class="text-medium">{{ __('Categories') }}:</span>
                                    <a
                                        href="{{ route('front.catalog') . '?category=' . $item->category->slug }}">{{ $item->category->name }}</a>
                                    @if ($item->subcategory->name)
                                        /
                                    @endif
                                    <a
                                        href="{{ route('front.catalog') . '?subcategory=' . $item->subcategory->slug }}">{{ $item->subcategory->name }}</a>
                                    @if ($item->childcategory->name)
                                        /
                                    @endif
                                    <a
                                        href="{{ route('front.catalog') . '?childcategory=' . $item->childcategory->slug }}">{{ $item->childcategory->name }}</a>
                                </div>
                                <div class="pt-1 mb-1"><span class="text-medium">{{ __('Tags') }}:</span>
                                    @if ($item->tags)
                                        @foreach (explode(',', $item->tags) as $tag)
                                            @if ($loop->last)
                                                <a
                                                    href="{{ route('front.catalog') . '?tag=' . $tag }}">{{ $tag }}</a>
                                            @else
                                                <a
                                                    href="{{ route('front.catalog') . '?tag=' . $tag }}">{{ $tag }}</a>,
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                @if ($item->item_type == 'normal')
                                    <div class="pt-1 mb-4"><span class="text-medium">{{ __('SKU') }}:</span>
                                        #{{ $item->sku }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class=" padding-top-3x mb-3" id="details">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                aria-selected="true">{{ $item->diamondAttribute ? __('Diamond specs & details') : __('Descriptions') }}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="specification-tab" data-bs-toggle="tab"
                                data-bs-target="#specification" type="button" role="tab"
                                aria-controls="specification" aria-selected="false">{{ __('Specifications') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content card">
                        <div class="tab-pane fade show active" id="description" role="tabpanel"
                            aria-labelledby="description-tab">
                            @if($item->diamondAttribute)
                                @php $d = $item->diamondAttribute; @endphp
                                <div class="diamond-spec-table">
                                    <h4 class="luxury-headline">Diamond Specifications</h4>
                                    <table class="spec-table">
                                        <tbody>
                                            @foreach([
                                                ['Shape', $d->shape],
                                                ['Carat Weight', filled($d->carat_weight ?? null) ? trim((string) $d->carat_weight).' ct' : null],
                                                ['Cut', $d->cut_grade],
                                                ['Colour', is_array($d->color_grade ?? null) ? implode(', ', $d->color_grade) : $d->color_grade],
                                                ['Clarity', is_array($d->clarity_grade ?? null) ? implode(', ', $d->clarity_grade) : $d->clarity_grade],
                                                ['Polish', $d->polish],
                                                ['Symmetry', $d->symmetry],
                                                ['Fluorescence', $d->fluorescence],
                                                ['Table %', filled($d->table_pct ?? null) ? $d->table_pct.'%' : null],
                                                ['Depth %', filled($d->depth_pct ?? null) ? $d->depth_pct.'%' : null],
                                                ['Measurements (L×W×D mm)',
                                                    (filled($d->length_mm) && filled($d->width_mm) && filled($d->depth_mm))
                                                        ? $d->length_mm.'×'.$d->width_mm.'×'.$d->depth_mm.' mm'
                                                        : null],
                                                ['Origin', $d->is_lab_grown ? 'Lab-grown' : 'Natural'],
                                                ['Certificate laboratory', filled($d->lab ?? null) ? $d->lab : null],
                                                ['Certificate number', filled($d->certificate_number ?? null) ? $d->certificate_number : null],
                                            ] as [$label, $value])
                                                @if($value !== null && $value !== '')
                                                    <tr>
                                                        <td class="spec-label">{{ $label }}</td>
                                                        <td class="spec-value">
                                                            @if($label === 'Certificate number' && filled($d->certificate_url ?? null))
                                                                <a href="{{ $d->certificate_url }}" target="_blank" rel="noopener">{{ $value }}</a>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if (filled(trim(strip_tags($item->details ?? ''))))
                                        <div class="product-long-description rte-details mt-4">
                                            {!! $item->details !!}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="product-long-description rte-details">
                                    {!! $item->details !!}
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane fade show" id="specification" role="tabpanel"
                            aria-labelledby="specification-tab">
                            <div class="comparison-table">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary">
                                    </thead>
                                    <tbody>
                                        <tr class="bg-secondary">
                                            <th class="text-uppercase">{{ __('Specifications') }}</th>
                                            <td><span class="text-medium">{{ __('Descriptions') }}</span></td>
                                        </tr>
                                        @if ($sec_name)
                                            @foreach (array_combine($sec_name, $sec_details) as $sname => $sdetail)
                                                <tr>
                                                    <th>{{ $sname }}</th>
                                                    <td>{{ $sdetail }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="text-center">
                                                <td colspan="2">{{ __('No Specifications') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Reviews-->
    <div class="container  review-area">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2 class="h3">{{ __('Latest Reviews') }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                @forelse ($reviews as $review)
                    <div class="single-review">
                        <div class="comment">
                            <div class="comment-author-ava"><img class="lazy"
                                    data-src="{{ \App\Helpers\ImageHelper::storageImageUrl($review->user->photo ?? null) }}"
                                    alt="Comment author">
                            </div>
                            <div class="comment-body">
                                <div class="comment-header d-flex flex-wrap justify-content-between">
                                    <div>
                                        <h4 class="comment-title mb-1">{{ $review->subject }}</h4>
                                        <span>{{ $review->user->first_name }}</span>
                                        <span class="ml-3">{{ $review->created_at->format('M d, Y') }}</span>
                                        @if (! empty($review->is_verified_purchase))
                                            <span class="badge badge-success ml-2">{{ __('Verified purchase') }}</span>
                                        @endif
                                        @if (filled($review->occasion ?? null))
                                            <span class="badge badge-secondary ml-1">{{ $review->occasion }}</span>
                                        @endif
                                    </div>
                                    <div class="mb-2">
                                        <div class="rating-stars">
                                            @php
                                                for ($i = 0; $i < $review->rating; $i++) {
                                                    echo "<i class = 'far fa-star filled'></i>";
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                                <p class="comment-text mt-2">{{ $review->review }}</p>
                                @if (! empty($review->review_photo ?? null))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $review->review_photo) }}" alt=""
                                            class="rounded border img-fluid" loading="lazy" style="max-height:220px;">
                                    </div>
                                @endif
                                @if (filled($review->metal_type_ordered ?? null) || filled($review->ring_size_ordered ?? null))
                                    <p class="small text-muted mt-2 mb-0">
                                        @if (filled($review->metal_type_ordered ?? null))
                                            <strong>{{ __('Metal') }}:</strong> {{ $review->metal_type_ordered }}
                                            @if (filled($review->ring_size_ordered ?? null))
                                                —
                                            @endif
                                        @endif
                                        @if (filled($review->ring_size_ordered ?? null))
                                            <strong>{{ __('Size') }}:</strong> {{ $review->ring_size_ordered }}
                                        @endif
                                    </p>
                                @endif

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-5">
                        {{ __('No Review') }}
                    </div>
                @endforelse
                <div class="row mt-15">
                    <div class="col-lg-12 text-center">
                        {{ $reviews->links() }}
                    </div>
                </div>

            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="d-inline align-baseline display-3 mr-1">
                                {{ round($item->reviews->avg('rating'), 2) }}</div>
                            <div class="d-inline align-baseline text-sm text-warning mr-1">
                                <div class="rating-stars">
                                    {!! Helper::renderStarRating($item->reviews->avg('rating')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="pt-3">
                            <label class="text-medium text-sm">5 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 5)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 5)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="100"
                                    aria-valuemin="{{ $item->reviews->where('rating', 5)->sum('rating') * 20 }}"
                                    aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">4 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 4)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 4)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="{{ $item->reviews->where('rating', 4)->sum('rating') * 20 }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">3 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 3)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('rating', 3)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="{{ $item->reviews->where('rating', 3)->sum('rating') * 20 }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">2 {{ __('stars') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 2)->count() }}</span></label>
                            <div class="progress margin-bottom-1x">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 2)->sum('rating') * 20 }}%; height: 2px;"
                                    aria-valuenow="{{ $item->reviews->where('rating', 2)->sum('rating') * 20 }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <label class="text-medium text-sm">1 {{ __('star') }} <span class="text-muted">-
                                    {{ $item->reviews->where('status', 1)->where('rating', 1)->count() }}</span></label>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ $item->reviews->where('status', 1)->where('rating', 1)->sum('rating') * 20 }}; height: 2px;"
                                    aria-valuenow="0"
                                    aria-valuemin="{{ $item->reviews->where('rating', 1)->sum('rating') * 20 }}"
                                    aria-valuemax="100"></div>
                            </div>
                        </div>
                        @if (Auth::user())
                            <div class="pb-2"><a class="btn btn-primary btn-block" href="#"
                                    data-bs-toggle="modal"
                                    data-bs-target="#leaveReview"><span>{{ __('Leave a Review') }}</span></a></div>
                        @else
                            <div class="pb-2"><a class="btn btn-primary btn-block"
                                    href="{{ route('user.login') }}"><span>{{ __('Login') }}</span></a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('front.catalog.partials.pdp-complete-the-look')

    @if (count($related_items) > 0)
        <div class="relatedproduct-section container padding-bottom-3x mb-1 s-pt-30">
            <!-- Related Products Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h3">{{ __('You May Also Like') }}</h2>
                    </div>
                </div>
            </div>
            <!-- Carousel-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="relatedproductslider owl-carousel">
                        @foreach ($related_items as $related)
                            <div class="slider-item">
                                <div class="product-card">

                                    @if (! $related->is_stock())
                                        <div
                                            class="product-badge bg-secondary border-default text-body
                                    ">
                                            {{ __('out of stock') }}</div>
                                    @endif
                                    <div class="product-thumb">
                                        <img class="lazy"
                                            data-src="{{ \App\Helpers\ImageHelper::storageImageUrl($related->thumbnail ?: $related->photo) }}"
                                            alt="Product">
                                        <div class="product-button-group">
                                            <a class="product-button wishlist_store"
                                                href="{{ route('user.wishlist.store', $related->id) }}"
                                                title="{{ __('Wishlist') }}"><i class="icon-heart"></i></a>
                                            @include('includes.item_footer', ['sitem' => $related])
                                        </div>
                                    </div>
                                    <div class="product-card-body">
                                        <div class="product-category"><a
                                                href="{{ route('front.catalog') . '?category=' . $related->category->slug }}">{{ $related->category->name }}</a>
                                        </div>
                                        <h3 class="product-title"><a
                                                href="{{ route('front.product', $related->slug) }}">
                                                {{ Str::limit($related->name, 35) }}
                                            </a></h3>
                                        <h4 class="product-price">
                                            @if ($related->previous_price != 0)
                                                <del>{{ PriceHelper::setPreviousPrice($related->previous_price) }}</del>
                                            @endif
                                            {{ \App\Services\JewelryDynamicPriceService::catalogCurrencyPrice($related) }}
                                        </h4>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif




    @auth
        <form class="modal fade ratingForm" action="{{ route('front.review.submit') }}" method="post" id="leaveReview"
            tabindex="-1" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Leave a Review') }}</h4>
                        <button class="close modal_close" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $user = Auth::user();
                        @endphp
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-name">{{ __('Your Name') }}</label>
                                    <input class="form-control" type="text" id="review-name"
                                        value="{{ $user->first_name }}" required>
                                </div>
                            </div>
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-email">{{ __('Your Email') }}</label>
                                    <input class="form-control" type="email" id="review-email"
                                        value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-subject">{{ __('Subject') }}</label>
                                    <input class="form-control" type="text" name="subject" id="review-subject" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="review-rating">{{ __('Rating') }}</label>
                                    <select name="rating" class="form-control" id="review-rating">
                                        <option value="5">5 {{ __('Stars') }}</option>
                                        <option value="4">4 {{ __('Stars') }}</option>
                                        <option value="3">3 {{ __('Stars') }}</option>
                                        <option value="2">2 {{ __('Stars') }}</option>
                                        <option value="1">1 {{ __('Star') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="review-occasion">{{ __('Occasion (optional)') }}</label>
                                <select name="occasion" id="review-occasion" class="form-control">
                                    <option value="">{{ __('—') }}</option>
                                    <option value="Engagement">{{ __('Engagement') }}</option>
                                    <option value="Wedding">{{ __('Wedding') }}</option>
                                    <option value="Anniversary">{{ __('Anniversary') }}</option>
                                    <option value="Birthday">{{ __('Birthday') }}</option>
                                    <option value="Self-gift">{{ __('Self-gift') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="review-photo">{{ __('Photo of your piece') }}</label>
                                <input type="file" class="form-control-file" id="review-photo" name="review_photo"
                                    accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="review-ring-size">{{ __('Ring size received (optional)') }}</label>
                                <input type="text" name="ring_size_ordered" id="review-ring-size" class="form-control"
                                    maxlength="48" autocomplete="off"
                                    placeholder="{{ __('e.g. US 7') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="review-metal">{{ __('Metal ordered (optional)') }}</label>
                                <input type="text" name="metal_type_ordered" id="review-metal" class="form-control"
                                    maxlength="80" autocomplete="off"
                                    placeholder="{{ __('e.g. 18K Yellow Gold') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="review-message">{{ __('Review') }}</label>
                            <textarea class="form-control" name="review" id="review-message" rows="8" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit"><span>{{ __('Submit Review') }}</span></button>
                    </div>
                </div>
            </div>
        </form>
    @endauth

@if (!empty($show_drop_hint ?? false))
    @include('front.catalog.partials.drop-hint-modal')
@endif

{{-- Ring Sizer Modal --}}
<div class="modal fade" id="ringSizerModal" tabindex="-1" aria-labelledby="ringSizerModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content luxury-modal">
            <div class="modal-header">
                <h5 class="modal-title luxury-headline" id="ringSizerModalLabel">{{ __('Ring size & fit guide') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">{{ __('Sizing varies slightly by brand and band width. This chart is a starting point—we include one complimentary resize on eligible rings within 60 days of delivery where noted on the product.') }}</p>

                <ul class="nav nav-tabs ring-guide-tabs mt-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="ring-tab-conv" data-bs-toggle="tab" data-bs-target="#ring-pane-conv" type="button" role="tab">{{ __('Conversion chart') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ring-tab-how" data-bs-toggle="tab" data-bs-target="#ring-pane-how" type="button" role="tab">{{ __('How to measure') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ring-tab-print" data-bs-toggle="tab" data-bs-target="#ring-pane-print" type="button" role="tab">{{ __('Printable strip') }}</button>
                    </li>
                </ul>
                <div class="tab-content pt-3">
                    <div class="tab-pane fade show active" id="ring-pane-conv" role="tabpanel" aria-labelledby="ring-tab-conv">
                        <table class="size-conversion-table">
                            <thead>
                                <tr>
                                    <th>{{ __('US / CA') }}</th>
                                    <th>{{ __('UK') }}</th>
                                    <th>{{ __('EU') }}</th>
                                    <th>{{ __('India') }}</th>
                                    <th>{{ __('Japan') }}</th>
                                    <th>{{ __('Inside Ø') }} <span class="fw-normal small">(mm)</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    ['5', 'J', '49', '9', '9', '15.7'],
                                    ['5.5', 'K', '50', '10', '10', '16.1'],
                                    ['6', 'L½', '52', '11', '12', '16.5'],
                                    ['6.5', 'M½', '54', '12', '13', '16.9'],
                                    ['7', 'N½', '55', '14', '14', '17.3'],
                                    ['7.5', 'O½', '57', '15', '15', '17.7'],
                                    ['8', 'P½', '58', '16', '16', '18.1'],
                                    ['8.5', 'Q½', '60', '17', '18', '18.5'],
                                    ['9', 'R½', '61', '18', '20', '18.9'],
                                    ['9.5', 'S½', '62', '19', '21', '19.4'],
                                    ['10', 'T½', '63', '20', '22', '19.8'],
                                    ['11', 'V½', '66', '22', '24', '20.7'],
                                    ['12', 'X½', '69', '24', '27', '21.7'],
                                ] as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td>{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="ring-pane-how" role="tabpanel" aria-labelledby="ring-tab-how">
                        <div class="row align-items-start g-3">
                            <div class="col-md-7">
                                <ol class="small mb-0 ps-3">
                                    <li class="mb-2">{{ __('Cut a thin strip of paper or string about 12 cm long.') }}</li>
                                    <li class="mb-2">{{ __('Wrap it snugly around the widest part of the finger (often the knuckle).') }}</li>
                                    <li class="mb-2">{{ __('Mark where the end meets, then lay flat and measure the length in millimetres—this is the inner circumference.') }}</li>
                                    <li class="mb-2">{{ __('Divide that length by π (≈ 3.14) to approximate the inner diameter and match the closest EU / US column in the chart tab.') }}</li>
                                    <li>{{ __('Measure at room temperature; fingers swell slightly during the day.') }}</li>
                                </ol>
                            </div>
                            <div class="col-md-5 text-center">
                                <svg class="ring-strip-diagram img-fluid" viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <ellipse cx="100" cy="70" rx="52" ry="62" fill="none" stroke="#333" stroke-width="3"/>
                                    <path d="M60 118 L140 118" stroke="#B8860B" stroke-width="2" marker-end="url(#arr)"/>
                                    <defs><marker id="arr" markerWidth="6" markerHeight="6" refX="5" refY="3" orient="auto"><path d="M0,0 L6,3 L0,6 Z" fill="#B8860B"/></marker></defs>
                                    <rect x="40" y="24" width="120" height="10" rx="2" fill="#ece8e0" stroke="#b8b0a0"/>
                                    <text x="100" y="32" font-size="7" text-anchor="middle" fill="#333">{{ __('Paper strip →') }}</text>
                                </svg>
                                <p class="tiny-caption text-muted mt-1">{{ __('Diagram: snug wrap at knuckle height') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ring-pane-print" role="tabpanel" aria-labelledby="ring-tab-print">
                        <p class="small">{{ __('Print this page at 100% scale (no “fit to page”). Cut along the dashed line and wrap around your finger—the edge should meet the circumference that feels snug.') }}</p>
                        <div class="ring-sizer-print border p-4 mt-3">
                            <p class="text-uppercase small letter-spacing mb-2">{{ __('Printable circumference strip') }} (mm)</p>
                            <div class="ring-mm-ruler mb-4" aria-hidden="true"></div>
                            <p class="hint-print small text-muted d-none">{{ __('Use your browser’s Print dialog on this tab only.') }}</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button type="button" class="btn-luxury" onclick="window.print()">{{ __('Print sizing strip') }}</button>
                            @if (\Illuminate\Support\Facades\File::exists(public_path('pdf/ring-sizer.pdf')))
                                <a href="{{ asset('pdf/ring-sizer.pdf') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm align-self-center">{{ __('Download PDF ring sizer') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($item->item_type == 'normal' && $item->is_stock())
    @php $pdpHasOptions = isset($attributes) && $attributes->count() > 0; @endphp
    <div id="pdp-sticky-atc" class="pdp-sticky-atc" aria-hidden="true">
        <div id="pdp-sticky-price" class="pdp-sticky-atc__price">{{ PriceHelper::grandCurrencyPrice($item) }}</div>
        <button type="button" class="btn btn-primary pdp-sticky-atc__btn" id="pdpStickyCartBtn">
            {{ $pdpHasOptions ? __('Configure / Add to cart') : __('Add to Cart') }}
        </button>
    </div>
@endif

<script>
(function () {
    var sentinel = document.getElementById('add-to-cart');
    var bar = document.getElementById('pdp-sticky-atc');
    var mq = typeof window.matchMedia === 'function' ? window.matchMedia('(max-width: 767px)') : null;

    function syncStickyPrice() {
        var mp = document.getElementById('main_price');
        var sp = document.getElementById('pdp-sticky-price');
        if (mp && sp) sp.textContent = mp.textContent;
    }

    if (sentinel && bar && window.IntersectionObserver && mq && mq.matches) {
        syncStickyPrice();
        var obs = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (e) {
                    var hide = e.isIntersecting === true || e.intersectionRatio > 0;
                    bar.classList.toggle('is-visible', !hide);
                    bar.setAttribute('aria-hidden', hide ? 'true' : 'false');
                    if (!hide) syncStickyPrice();
                });
            },
            { threshold: [0, 0.05] }
        );
        obs.observe(sentinel);

        var mp = document.getElementById('main_price');
        if (window.MutationObserver && mp)
            new MutationObserver(syncStickyPrice).observe(mp, { childList: true, characterData: true, subtree: true });

        document.getElementById('pdpStickyCartBtn') &&
            document.getElementById('pdpStickyCartBtn').addEventListener('click', function () {
                var b = document.getElementById('add_to_cart');
                if (b) b.click();
            });
    }
})();

function selectRingSize(btn) {
    var selectId = btn.getAttribute('data-select-id');
    var value = btn.getAttribute('data-value');
    btn.closest('.size-grid').querySelectorAll('.size-btn').forEach(function(b) {
        b.classList.remove('size-btn--active');
    });
    btn.classList.add('size-btn--active');
    var sel = document.getElementById(selectId);
    if (sel) {
        sel.value = value;
        sel.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function syncCustomRingSize(input, selectId) {
    var v = (input && input.value) ? String(input.value).trim() : '';
    var sel = document.getElementById(selectId);
    if (!sel) return;

    // Clear button selection when typing custom size
    var wrap = input.closest ? input.closest('.ring-size-selector') : null;
    if (wrap) {
        var grid = wrap.querySelector('.size-grid');
        if (grid) {
            grid.querySelectorAll('.size-btn').forEach(function (b) {
                b.classList.remove('size-btn--active');
            });
        }
    }

    if (!v) {
        return;
    }

    var rs = document.getElementById('pdp_ring_size');
    if (rs) rs.value = v;

    // Ensure option exists in hidden select so existing cart JS keeps working
    var exists = false;
    for (var i = 0; i < sel.options.length; i++) {
        if (String(sel.options[i].value) === v) {
            exists = true;
            break;
        }
    }
    if (!exists) {
        var opt = document.createElement('option');
        opt.value = v;
        opt.textContent = v;
        // no data-href for custom sizes (no price delta)
        opt.setAttribute('data-type', sel.options[0] ? sel.options[0].getAttribute('data-type') : '');
        opt.setAttribute('data-href', '');
        opt.setAttribute('data-target', '0');
        sel.appendChild(opt);
    }
    sel.value = v;
    sel.dispatchEvent(new Event('change', { bubbles: true }));
}

function requestDiamondInspection(itemId) {
    fetch(@json(route('diamonds.inspection.request')), {
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
            if (window.iziToast && data.message) {
                iziToast.info({ message: data.message, position: 'topRight', timeout: 6000 });
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(function () {});
}
</script>

@endsection
