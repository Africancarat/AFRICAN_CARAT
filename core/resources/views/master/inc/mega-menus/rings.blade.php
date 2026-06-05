{{-- Wedding / fashion rings mega --}}
<div class="row luxury-mega-row g-0">
    <div class="col-lg-4 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Shop by style') }}</p>
        @foreach ([
            ['label' => __('All rings'), 'href' => url('/catalog?category=Wedding-Rings')],
            ['label' => __('Eternity rings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Eternity-Rings']))],
            ['label' => __('Curved rings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Curved-Rings']))],
            ['label' => __('Anniversary rings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Anniversary-Rings']))],
            ['label' => __("Men's wedding rings"), 'href' => url('/catalog?' . http_build_query(['subcategory' => "Men-s-wedding-Rings"]))],
        ] as $row)
            <a href="{{ $row['href'] }}" class="luxury-mega-link">
                <span>{{ $row['label'] }}</span>
            </a>
        @endforeach
        <div class="luxury-mega-divider"></div>
        <a href="{{ url('/wedding-rings') }}" class="luxury-mega-link luxury-mega-link--footer">
            <span>{{ __('Best-selling wedding rings') }}</span>
        </a>
    </div>
    <div class="col-lg-4 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Women') }}</p>
        @foreach ([__('Diamond wedding rings'), __('Classic wedding rings'), __('Curved wedding rings'), __("Fashion wedding rings"), __("Women's wedding rings")] as $label)
            <a href="{{ route('front.catalog', ['search' => $label]) }}" class="luxury-mega-link"><span>{{ $label }}</span></a>
        @endforeach
        <p class="luxury-mega-subkicker mt-3">{{ __('Men') }}</p>
        @foreach ([__('Diamond bands'), __('Classic bands'), __('Carved bands'), __('Alternative metals'), __("Men's wedding rings")] as $label)
            <a href="{{ route('front.catalog', ['search' => $label]) }}" class="luxury-mega-link"><span>{{ $label }}</span></a>
        @endforeach
    </div>
    @include('master.inc.mega-menus.partials.image-column', [
        'image' => 'African carat/2.png',
        'href' => url('/wedding-rings'),
        'alt' => __('Wedding rings'),
    ])
</div>
