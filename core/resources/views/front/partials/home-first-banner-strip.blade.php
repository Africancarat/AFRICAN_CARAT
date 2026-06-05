{{-- Home "first banner": 5 square category tiles (luxury strip). Expects $banner_first array. --}}
{{-- Collection images from African carat/1.png … 5.png (admin images used only if file missing). --}}
@php
    $africanCaratCollectionDir = 'African carat';
    $africanCaratCollectionFiles = [1 => '1.png', 2 => '2.png', 3 => '3.png', 4 => '4.png', 5 => '5.png'];
    $collectionImageUrl = function (?string $relativePath): ?string {
        $relativePath = ltrim(trim((string) $relativePath), '/');
        if ($relativePath === '' || ! is_file(public_path('storage/images/' . $relativePath))) {
            return null;
        }

        return url('/core/public/storage/images/' . implode('/', array_map('rawurlencode', explode('/', $relativePath))));
    };
@endphp
<div class="luxury-first-banner-strip bannner-section mt-60">
    <div class="container">
        <h2 class="luxury-first-banner-strip__title">{{ __('Shop by collection') }}</h2>
        <div class="luxury-first-banner-strip__grid">
            @for ($i = 1; $i <= 5; $i++)
                @php
                    $img = trim((string) ($banner_first['img' . $i] ?? ''));
                    $africanCaratFile = $africanCaratCollectionDir . '/' . ($africanCaratCollectionFiles[$i] ?? '');
                    if (is_file(public_path('storage/images/' . $africanCaratFile))) {
                        $img = $africanCaratFile;
                    }
                    $imgUrl = $collectionImageUrl($img);
                    $href = trim((string) ($banner_first['firsturl' . $i] ?? ''));
                    if ($href === '') {
                        $href = '#';
                    }
                @endphp
                <div class="luxury-first-banner-strip__cell">
                    <a href="{{ $href }}" class="luxury-category-tile">
                        <div class="luxury-category-tile__media">
                            @if ($imgUrl)
                                <img src="{{ $imgUrl }}"
                                    alt="{{ strip_tags((string) ($banner_first['title' . $i] ?? '')) }}">
                            @endif
                        </div>
                        <div class="luxury-category-tile__label">
                            @if (!empty($banner_first['subtitle' . $i]))
                                <p class="luxury-category-tile__subtitle">{{ $banner_first['subtitle' . $i] }}</p>
                            @endif
                            @if (!empty($banner_first['title' . $i]))
                                <h4 class="luxury-category-tile__title">{{ $banner_first['title' . $i] }}</h4>
                            @endif
                        </div>
                    </a>
                </div>
            @endfor
        </div>
    </div>
</div>
