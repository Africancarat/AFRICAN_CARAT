<div class="row g-3" id="main_div">
    @if($items->count() > 0)
        @if ($checkType != 'list')
            @foreach ($items as $item)
            <div class="col-xxl-3 col-md-4 col-6">
                <div class="product-card catalog-card">
                    @if (! $item->is_stock())
                    <div class="product-badge bg-secondary border-default text-body">{{ __('out of stock') }}</div>
                    @endif
                <div class="product-thumb">
                    @php
                        $images = json_decode($item->itemPrice->image ?? '[]', true);

                        $firstImage = $images[0]['image'] ?? null;
                    @endphp

                    <div style="font-size:10px;color:red">
                        {{ $firstImage }}
                    </div>
                    @php
                        $catalogImage = null;

                        if ($item->itemPrice && $item->itemPrice->image) {

                            $metalImages = json_decode($item->itemPrice->image, true);

                            if (
                                is_array($metalImages)
                                && isset($metalImages[0]['images'][0])
                                && !empty($metalImages[0]['images'][0])
                            ) {
                                $catalogImage = $metalImages[0]['images'][0];
                            }
                        }
                    @endphp
                    <img
                            class="lazy"
                            src="{{ $catalogImage
        ? \App\Helpers\ImageHelper::storageImageUrl($catalogImage)
        : \App\Helpers\ImageHelper::storageImageUrl($item->thumbnail ?: $item->photo) }}"
                            alt="{{ $item->name }}"
                    >
                    <pre>{{ asset('storage/' . $catalogImage) }}</pre>
                    <div class="product-button-group">
                        <a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                        @include('includes.item_footer',['sitem' => $item])
                    </div>
                </div>
                <div class="product-card-body">
                    <div class="product-category">
                        <a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a>
                    </div>
                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                        {{ Str::limit($item->name, 38) }}
                    </a></h3>
                    <h4 class="product-price">
                        @if ($item->previous_price !=0)
                        <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                        @endif
                        {{\App\Services\JewelryDynamicPriceService::catalogCurrencyPrice($item)}}
                    </h4>
                </div>

                </div>
            </div>
            @endforeach
        @else
            @foreach ($items as $item)
                <div class="col-lg-12">
                    <div class="product-card product-list catalog-card">
                        <div class="product-thumb" >
                            @if (! $item->is_stock())
                            <div class="product-badge bg-secondary border-default text-body">{{ __('out of stock') }}</div>
                            @endif

                            <img class="lazy" src="{{ \App\Helpers\ImageHelper::storageImageUrl($item->thumbnail ?: $item->photo) }}" alt="Product">
                            <div class="product-button-group">
                                <a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                @include('includes.item_footer',['sitem' => $item])
                            </div>
                        </div>
                            <div class="product-card-inner">
                                <div class="product-card-body">
                                    <div class="product-category"><a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                                        {{ Str::limit($item->name, 52) }}
                                    </a></h3>
                                    <h4 class="product-price">
                                        @if ($item->previous_price !=0)
                                        <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                                        @endif
                                        {{\App\Services\JewelryDynamicPriceService::catalogCurrencyPrice($item)}}
                                    </h4>
                                    <p class="text-sm sort_details_show  text-muted hidden-xs-down my-1">
                                    {{ Str::limit(strip_tags($item->sort_details), 100) }}
                                    </p>
                                </div>


                            </div>
                        </div>
                </div>
            @endforeach
        @endif
    @else
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="h4 mb-0">{{ __('No Product Found') }}</h4>
                </div>
            </div>
        </div>
    @endif
</div>


<!-- Pagination-->
<div class="row mt-15" id="item_pagination">
    <div class="col-lg-12 text-center">
        {{$items->links()}}
    </div>
</div>

<script type="text/javascript" src="{{asset('assets/front/js/catalog.js')}}"></script>
