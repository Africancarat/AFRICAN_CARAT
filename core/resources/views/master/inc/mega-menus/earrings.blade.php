{{-- Earrings mega + subcategories (luxury modal) --}}
<div class="row luxury-mega-row g-0">
    <div class="col-lg-4 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Shop by earing') }}</p>

        @foreach ([
            ['label' => __('All Earrings'), 'href' => url('/catalog?' . http_build_query(['category' => 'Earrings']))],
            ['label' => __('Stud Earrings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Stud-Earrings']))],
            ['label' => __('Huggies & Hoops'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Huggies-Hoops']))],
            ['label' => __('Bridal Earrings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Bridal-Earrings']))],
            ['label' => __('Dangle Earrings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Dangle-Earrings']))],
            ['label' => __('Diamond Earrings'), 'href' => url('/catalog?' . http_build_query(['subcategory' => 'Diamond-Earrings']))],
        ] as $row)
            <a href="{{ $row['href'] }}" class="luxury-mega-link">
                <span>{{ $row['label'] }}</span>
            </a>
        @endforeach

        <div class="luxury-mega-divider"></div>
        <a href="{{ url('/catalog?' . http_build_query(['subcategory' => 'Earrings'])) }}"
            class="luxury-mega-link luxury-mega-link--footer">
            <span>{{ __('Best-selling earrings') }}</span>
        </a>
    </div>

    @include('master.inc.mega-menus.partials.image-column', [
        'image' => 'African carat/3.png',
        'href' => url('/catalog?' . http_build_query(['category' => 'Earrings'])),
        'alt' => __('Earrings'),
    ])
    @include('master.inc.mega-menus.partials.image-column', [
        'image' => 'African carat/4.png',
        'href' => url('/catalog?' . http_build_query(['subcategory' => 'Dangle-Earrings'])),
        'alt' => __('Dangle earrings'),
    ])
</div>
