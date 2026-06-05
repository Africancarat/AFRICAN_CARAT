{{-- Harry Winston–style mega menu image column (right side) --}}
@php
    $megaImageKey = $imageKey ?? null;
    $megaImageFile = $image ?? null;

    if ($megaImageFile) {
        $explicitPath = public_path('storage/images/' . ltrim($megaImageFile, '/'));
        if (! is_file($explicitPath)) {
            $megaImageFile = null;
        }
    }

    if (! $megaImageFile && $megaImageKey) {
        $africanCaratDir = 'African carat';
        $africanCaratMap = [
            'engagement' => '1',
            'necklaces' => '2',
            'earrings' => '3',
            'rings' => '4',
            'diamonds' => '5',
        ];

        if (isset($africanCaratMap[$megaImageKey])) {
            $baseName = $africanCaratMap[$megaImageKey];
            $diskDir = public_path('storage/images/' . $africanCaratDir);
            foreach (['webp', 'png', 'jpg', 'jpeg'] as $ext) {
                $candidate = $diskDir . DIRECTORY_SEPARATOR . $baseName . '.' . $ext;
                if (is_file($candidate)) {
                    $megaImageFile = $africanCaratDir . '/' . $baseName . '.' . $ext;
                    break;
                }
            }
        }
    }

    $megaImageAlt = $alt ?? ($setting->title ?? 'African Carat');
    $megaHref = $href ?? route('front.index');
    $megaImageUrl = $megaImageFile
        ? url('/core/public/storage/images/' . implode('/', array_map('rawurlencode', explode('/', $megaImageFile))))
        : null;
@endphp
@if ($megaImageFile && $megaImageUrl)
<div class="{{ $columnClass ?? 'col-lg-4' }} luxury-mega-col luxury-mega-col--media">
    <div class="luxury-mega-media">
        <a href="{{ $megaHref }}" class="luxury-mega-media__link" aria-label="{{ $megaImageAlt }}">
            <img class="luxury-mega-media__img"
                src="{{ $megaImageUrl }}"
                alt="{{ $megaImageAlt }}"
                width="480" height="360" loading="lazy" decoding="async">
        </a>
    </div>
</div>
@endif
