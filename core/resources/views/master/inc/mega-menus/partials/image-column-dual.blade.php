{{-- Two stacked images in the mega menu media column (earrings) --}}
@php
    $africanCaratDir = 'African carat';
    $africanCaratMap = [
        'earrings' => ['3', 'earrings-1', 'earrings1'],
        'earrings-2' => ['6', '3-2', 'earrings-2', 'earrings2'],
    ];
    $megaImagesExplicit = $images ?? [];

    $resolveMegaImage = function (?string $key, ?string $explicitFile = null) use ($africanCaratDir, $africanCaratMap): ?string {
        if ($explicitFile) {
            $path = public_path('storage/images/' . ltrim($explicitFile, '/'));
            return is_file($path) ? ltrim($explicitFile, '/') : null;
        }

        if (! $key || ! isset($africanCaratMap[$key])) {
            return null;
        }

        $diskDir = public_path('storage/images/' . $africanCaratDir);
        foreach ($africanCaratMap[$key] as $baseName) {
            foreach (['webp', 'png', 'jpg', 'jpeg'] as $ext) {
                $candidate = $diskDir . DIRECTORY_SEPARATOR . $baseName . '.' . $ext;
                if (is_file($candidate)) {
                    return $africanCaratDir . '/' . $baseName . '.' . $ext;
                }
            }
        }

        return null;
    };

    $megaImageKeys = $imageKeys ?? ['earrings', 'earrings-2'];
    $megaHrefs = $hrefs ?? [];
    $megaAlts = $alts ?? [];
    $megaSlides = [];

    foreach ($megaImageKeys as $i => $key) {
        $explicit = $megaImagesExplicit[$i] ?? null;
        $file = $resolveMegaImage($key, $explicit);
        if (! $file) {
            continue;
        }
        $megaSlides[] = [
            'url' => url('/core/public/storage/images/' . implode('/', array_map('rawurlencode', explode('/', $file)))),
            'href' => $megaHrefs[$i] ?? ($href ?? route('front.index')),
            'alt' => $megaAlts[$i] ?? ($alt ?? ($setting->title ?? 'African Carat')),
        ];
    }
@endphp
@if (count($megaSlides) > 0)
<div class="col-lg-4 luxury-mega-col luxury-mega-col--media luxury-mega-col--media-dual">
    <div class="luxury-mega-media-stack">
        @foreach ($megaSlides as $slide)
            <div class="luxury-mega-media luxury-mega-media--stacked">
                <a href="{{ $slide['href'] }}" class="luxury-mega-media__link" aria-label="{{ $slide['alt'] }}">
                    <img class="luxury-mega-media__img"
                        src="{{ $slide['url'] }}"
                        alt="{{ $slide['alt'] }}"
                        width="480" height="280" loading="lazy" decoding="async">
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif
