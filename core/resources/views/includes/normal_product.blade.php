@forelse ($items as $item)
            <div class="col-gd">
                <div class="product-card luxury-card">
                    <div class="product-thumb" >
                        {{-- Badges removed for luxury positioning --}}
                            <img class="lazy" data-src="{{ \App\Helpers\ImageHelper::storageImageUrl($item->thumbnail ?: $item->photo) }}" alt="Product">
                            <div class="product-button-group"><a class="product-button wishlist_store" href="{{route('user.wishlist.store',$item->id)}}" title="{{__('Wishlist')}}"><i class="icon-heart"></i></a>
                                @include('includes.item_footer',['sitem' => $item])
                            </div>
                    </div>
                    <div class="product-card-body luxury-card__body">
                        <div class="product-category"><a href="{{route('front.catalog').'?category='.$item->category->slug}}">{{$item->category->name}}</a></div>
                        <h3 class="product-title luxury-card__title"><a href="{{route('front.product',$item->slug)}}">
                            {{ Str::limit($item->name, 45) }}
                        </a></h3>
                        <h4 class="product-price luxury-card__price">
                            @if ($item->previous_price !=0)
                            <del>{{PriceHelper::setPreviousPrice($item->previous_price)}}</del>
                            @endif
                            {{PriceHelper::grandCurrencyPrice($item)}}
                            </h4>
                    </div>

                </div>
            </div>
            @empty
            <div class="card">
                <div class="card-body text-center">
                    {{__('No Product Found')}}
                </div>
            </div>
            @endforelse

            <script type="text/javascript" src="{{asset('assets/front/js/extraindex.js')}}"></script>
