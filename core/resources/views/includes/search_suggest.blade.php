
<div class="s-r-inner">
    @foreach ($items as $item)
    <div class="product-card p-col">
        <a class="product-thumb" href="{{route('front.product',$item->slug)}}">
            <img class="lazy" alt="Product" src="{{ \App\Helpers\ImageHelper::storageImageUrl($item->thumbnail ?: $item->photo) }}" style=""></a>
        <div class="product-card-body">
            <h3 class="product-title"><a href="{{route('front.product',$item->slug)}}">
                {{ Str::limit($item->name, 35) }}
            </a></h3>
            <h4 class="product-price">
                {{\App\Services\JewelryDynamicPriceService::catalogCurrencyPrice($item)}}
            </h4>
        </div>
    </div>
    @endforeach
    
</div>
<div class="bottom-area">
    <a id="view_all_search_" href="javascript:;">{{ __('View all result') }}</a>
</div>