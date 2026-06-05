@if (isset($complete_the_look_items) && $complete_the_look_items->isNotEmpty())
    <div class="complete-the-look container padding-bottom-3x mb-1 s-pt-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2 class="h3 luxury-headline">{{ __('Complete the look') }}</h2>
                    <p class="small text-muted max-width-legend">{{ __('Hand-picked pairing ideas — coordinating wedding bands, studs, or pendants for the same celebration.') }}</p>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @foreach ($complete_the_look_items as $citem)
                <div class="col-md-4">
                    <div class="complete-look-card border h-100 p-3" style="border-color:#D4C5A9!important;">
                        @php $cimg = \App\Helpers\ImageHelper::storageImageUrl($citem->thumbnail ?: $citem->photo); @endphp
                        <a href="{{ route('front.product', $citem->slug) }}" class="d-block text-center mb-3">
                            <img src="{{ $cimg }}" class="img-fluid" alt="{{ $citem->name }}" style="max-height:260px;object-fit:contain;">
                        </a>
                        <h3 class="h6"><a href="{{ route('front.product', $citem->slug) }}" class="text-dark">{{ $citem->name }}</a></h3>
                        <p class="small text-muted mb-2">{{ Str::limit($citem->sort_details, 90) }}</p>
                        <p class="h6 mb-0">{{ PriceHelper::grandCurrencyPrice($citem) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
