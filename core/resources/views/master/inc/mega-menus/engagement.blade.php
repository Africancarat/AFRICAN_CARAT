{{-- Engagement mega (column layout + deep links) --}}
<div class="row luxury-mega-row g-0">
    <div class="col-lg-3 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Shop by style') }}</p>
        @foreach ([
            ['label' => __('rings'), 'href' => url('/catalog?category=Engagement-Rings')],
            ['label' => __('necklace'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'necklace']))],
            ['label' => __('earrings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'earrings']))],
            ['label' => __('pendants'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'pendants']))],

        ] as $row)
            <a href="{{ $row['href'] }}" class="luxury-mega-link">
                <span>{{ $row['label'] }}</span>
            </a>
        @endforeach
        <div class="luxury-mega-divider"></div>

        <p class="luxury-mega-kicker">{{ __('Design your own engagement ring') }}</p>
        <a href="{{ route('front.catalog') }}" class="luxury-mega-link"><span>{{ __('Start with a setting') }}</span></a>
        <a href="{{ route('diamonds.index') }}" class="luxury-mega-link"><span>{{ __('Start with a diamond') }}</span></a>
        <a href="{{ route('diamonds.index', ['lab_grown' => 1]) }}" class="luxury-mega-link"><span>{{ __('Start with a lab-grown diamond') }}</span></a>
        <a href="{{ route('front.catalog') }}" class="luxury-mega-link"><span>{{ __('Start with a gemstone') }}</span></a>
        <div class="luxury-mega-divider"></div>
        <a href="{{ route('front.catalog') }}" class="luxury-mega-link luxury-mega-link--emphasis"><span>{{ __('Ready-to-ship engagement rings') }} <span class="luxury-mega-badge">New</span></span></a>
        <a href="{{ url('/engagement-rings') }}" class="luxury-mega-link"><span>{{ __("Explore men's engagement rings") }}</span></a>
        <a href="{{ route('front.catalog') }}" class="luxury-mega-link"><span>{{ __('Top engagement rings') }}</span></a>
        <a href="{{ url('/engagement-rings') }}" class="luxury-mega-link luxury-mega-link--footer"><span>{{ __('Shop all engagement') }}</span></a>
    </div>
    <div class="col-lg-3 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Customize your engagement ring') }}</p>
        <a href="{{ route('education.guides.show', ['slug' => 'ring-settings']) }}" class="luxury-mega-link"><span>{{ __('The ring studio') }}</span></a>
        <p class="luxury-mega-subkicker mt-3">{{ __('Engagement ring styles') }}</p>
        <div class="luxury-mega-grid">
            @foreach ([__('Solitaire'), __('Bezel'), __('Pavé'), __('Halo'), __('Channel-set'), __('Hidden halo'), __('Side-stone'), __('Three-stone')] as $label)
                <a href="{{ route('front.catalog', ['search' => $label]) }}" class="luxury-mega-pill">
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
        <p class="luxury-mega-subkicker mt-3">{{ __('More styles') }}</p>
        <div class="luxury-mega-grid">
            @foreach ([__('Tension'), __('Unique'), __('Floral'), __('Cathedral'), __('Tiara'), __('Cluster'), __('Vintage')] as $label)
                <a href="{{ route('front.catalog', ['search' => $label]) }}" class="luxury-mega-pill">
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @include('master.inc.mega-menus.partials.image-column', [
        'columnClass' => 'col-lg-3',
        'image' => 'African carat/2.png',
        'href' => url('/engagement-rings'),
        'alt' => __('Engagement rings'),
    ])
    @include('master.inc.mega-menus.partials.image-column', [
        'columnClass' => 'col-lg-3',
        'image' => 'African carat/1.png',
        'href' => url('/catalog?' . http_build_query(['category' => 'Jewellery'])),
        'alt' => __('Bridal jewelry'),
    ])
</div>
