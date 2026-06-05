{{-- Diamonds mega: loose search + design paths + jewelry + promo image column --}}
<div class="row luxury-mega-row g-0">
    <div class="col-lg-4 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Design your own engagement ring') }}</p>
        <a href="{{ route('diamonds.index') }}" class="luxury-mega-link"><span>{{ __('Start with a diamond') }}</span></a>
        <a href="{{ route('diamonds.index', ['lab_grown' => 1]) }}" class="luxury-mega-link"><span>{{ __('Start with a lab-grown diamond') }}</span></a>
        <a href="{{ route('diamonds.index') }}" class="luxury-mega-link"><span>{{ __('Start with a fancy colour diamond') }}</span></a>
        <a href="{{ route('diamonds.index', ['lab_grown' => 1]) }}" class="luxury-mega-link"><span>{{ __('Start with a fancy lab-grown diamond') }}</span></a>
        <a href="{{ route('front.catalog') }}" class="luxury-mega-link"><span>{{ __('Start with a setting') }}</span></a>
        <a href="{{ route('education.guides.show', ['slug' => 'ring-settings']) }}" class="luxury-mega-link"><span>{{ __('The ring studio') }}</span></a>
        <div class="luxury-mega-divider"></div>
        <a href="{{ route('diamonds.index', ['lab_grown' => 1]) }}" class="luxury-mega-link luxury-mega-link--accent"><span>{{ __('Lab-grown diamonds — featured') }}</span></a>
        <p class="luxury-mega-subkicker mt-3">{{ __('Premier diamond collection') }}</p>
        <a href="{{ route('diamonds.index') }}" class="luxury-mega-link"><span>{{ __('True hearts & ideal cuts') }}</span></a>
        <a href="{{ route('diamonds.index') }}" class="luxury-mega-link luxury-mega-link--footer"><span>{{ __('Shop all diamonds') }}</span></a>
    </div>
    <div class="col-lg-4 luxury-mega-col">
        <p class="luxury-mega-kicker">{{ __('Loose diamonds') }}</p>
        <div class="luxury-mega-grid luxury-mega-grid--shapes">
            @foreach (['Round', 'Princess', 'Cushion', 'Emerald', 'Pear', 'Oval', 'Radiant', 'Asscher', 'Marquise', 'Heart'] as $shape)
                <a href="{{ route('diamonds.index', ['shape' => $shape]) }}" class="luxury-mega-pill luxury-mega-pill--with-icon luxury-mega-pill--shape">
                    @include('front.diamonds.partials.shape-svg', ['key' => $shape, 'svgClass' => 'luxury-mega-ico luxury-mega-ico--shape'])
                    <span>{{ $shape }}</span>
                </a>
            @endforeach
            <a href="{{ route('diamonds.index', ['shape' => 'Octagon']) }}" class="luxury-mega-pill luxury-mega-pill--with-icon luxury-mega-pill--shape">
                @include('front.diamonds.partials.shape-svg', ['key' => 'Octagon', 'svgClass' => 'luxury-mega-ico luxury-mega-ico--shape'])
                <span>{{ __('Octagon') }} <span class="luxury-mega-badge">New</span></span>
            </a>
        </div>
        <p class="luxury-mega-subkicker mt-3">{{ __('Fancy colour diamonds') }}</p>
        <div class="luxury-mega-grid">
            @foreach ([['code' => 'yellow', 'label' => __('Yellow')], ['code' => 'pink', 'label' => __('Pink')], ['code' => 'purple', 'label' => __('Purple')], ['code' => 'blue', 'label' => __('Blue')], ['code' => 'green', 'label' => __('Green')], ['code' => 'orange', 'label' => __('Orange')], ['code' => 'brown', 'label' => __('Brown')], ['code' => 'black', 'label' => __('Black')]] as $fc)
                <a href="{{ route('front.catalog', ['search' => $fc['label'].' '.__('diamond')]) }}" class="luxury-mega-pill luxury-mega-pill--with-icon luxury-mega-pill--fancy luxury-mega-pill--fancy-{{ $fc['code'] }}">
                    <span class="luxury-mega-swatch luxury-mega-swatch--fancy-{{ $fc['code'] }}" aria-hidden="true"></span>
                    <span>{{ $fc['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @include('master.inc.mega-menus.partials.image-column', [
        'image' => 'African carat/5.png',
        'imageKey' => 'diamonds',
        'href' => route('diamonds.index'),
        'alt' => __('Diamonds'),
    ])
</div>
