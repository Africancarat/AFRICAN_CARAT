{{-- Necklaces mega --}}
<div class="row luxury-mega-row g-0">
    <div class="col-lg-4 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Shop all necklaces') }}</p>
        @foreach ([
            ['label' => __('All Necklaces'), 'href' => url('/catalog?' . http_build_query(['search' => 'Necklace']))],
            ['label' => __('Pendant'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Pendant']))],
            ['label' => __('Chain Necklaces'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Chain-Necklace']))],
            ['label' => __('Pendant Necklaces'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Pendant-Necklace']))],
            ['label' => __('Layering Necklaces'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Layering-Necklace']))],
            ['label' => __('Choker Necklaces'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Choker-Necklace']))],
        ] as $row)
            <a href="{{ $row['href'] }}" class="luxury-mega-link">
                <span>{{ $row['label'] }}</span>
            </a>
        @endforeach

        @foreach ([
            __('Diamond necklaces'),
            __('Gold necklaces'),
            __('Cross necklaces'),
            __("Men's necklaces"),
            __('Cuban chains'),
            __('Pearl necklaces'),
            __('Birthstone necklaces'),
            __('Gemstone necklaces'),
        ] as $label)
            <a href="{{ route('front.catalog', ['search' => $label]) }}" class="luxury-mega-link">
                <span>{{ $label }}</span>
            </a>
        @endforeach
        <div class="luxury-mega-divider"></div>
        <a href="{{ route('front.catalog', ['search' => 'necklace']) }}" class="luxury-mega-link luxury-mega-link--footer">
            <span>{{ __('Best-selling necklaces') }}</span>
        </a>
    </div>
    <div class="col-lg-4 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Design your own') }}</p>
        <a href="{{ route('front.catalog', ['search' => 'pendant']) }}" class="luxury-mega-link"><span>{{ __('Pendants') }}</span></a>
        <a href="{{ route('diamonds.index') }}" class="luxury-mega-link"><span>{{ __('Natural diamond') }}</span></a>
        <a href="{{ route('diamonds.index', ['lab_grown' => 1]) }}" class="luxury-mega-link"><span>{{ __('Lab-grown diamond') }}</span></a>
    </div>
    @include('master.inc.mega-menus.partials.image-column', [
        'imageKey' => 'necklaces',
        'href' => url('/catalog?' . http_build_query(['search' => 'Necklace'])),
        'alt' => __('Necklaces'),
    ])
</div>
