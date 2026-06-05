
  @if ($items->count() > 0)


  <div class="col-lg-12">
    <div class="popular-category-slider  owl-carousel" id="">
        @foreach ($items as $item)
        <div class="slider-item">
            <div class="product-card">
                <div class="product-thumb">
                    @if (!$item->is_stock())
                        <div class="product-badge bg-secondary border-default text-body
                        ">{{__('out of stock')}}</div>
                    @endif
                        <img class="lazy" data-src="{{ \App\Helpers\ImageHelper::storageImageUrl($item->thumbnail ?: $item->photo) }}" alt="Product">
                        <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                            @include('includes.item_footer',['sitem' => $item])
                        </div>
                </div>
                <div class="product-card-body">
                    <div class="product-category"><a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                    <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                        {{ Str::limit($item->name, 35) }}
                    </a></h3>
                    <h4 class="product-price">
                        @if ($item->previous_price !=0)
                        <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                        @endif
                        {{PriceHelper::grandCurrencyPrice($item)}}
                        </h4>
                </div>

            </div>
        </div>
       @endforeach
    </div>
</div>

@else
<div class="card">
    <div class="card-body text-center ">
        {{__('No Product Found')}}
    </div>
</div>
@endif

<script type="text/javascript" src="{{asset('assets/front/js/extraindex.js')}}"></script>
