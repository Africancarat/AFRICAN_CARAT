{{-- Home hero: slider video link, or default file in storage/images --}}
@php
    $heroStorageVideo = 'African_Carat_Ads_3.mp4';
    $heroStorageVideoDisk = public_path('storage/images/' . $heroStorageVideo);
    $heroStorageVideoUrl = is_file($heroStorageVideoDisk)
        ? '/core/public/storage/images/' . $heroStorageVideo
        : null;

    $hero = $sliders->first() ?? null;
    $heroVideo = null;

    if ($hero && $hero->link && preg_match('/\.(mp4|webm)(\?|#|$)/i', trim((string) $hero->link))) {
        $heroVideo = trim((string) $hero->link);
    } elseif ($heroStorageVideoUrl) {
        $heroVideo = $heroStorageVideoUrl;
    }

    $heroPoster = null;
    if ($hero && $hero->photo) {
        $heroPoster = class_exists(\App\Helpers\ImageHelper::class)
            ? \App\Helpers\ImageHelper::storageImageUrl($hero->photo)
            : url('/core/public/storage/images/' . $hero->photo);
    } else {
        foreach (['African carat/3.png', 'AfricanCarat-Logo/2.png'] as $posterFile) {
            if (is_file(public_path('storage/images/' . $posterFile))) {
                $heroPoster = url('/core/public/storage/images/' . implode('/', array_map('rawurlencode', explode('/', $posterFile))));
                break;
            }
        }
    }

    if (! $heroPoster) {
        $heroPoster = asset('images/hero-diamond.jpg');
    }

    $heroTitle = null;
    if ($hero && $hero->title && $hero->title !== 'theme 4') {
        $heroTitle = e($hero->title);
        if ($hero->details && $hero->details !== 'theme4') {
            $heroTitle .= '<br><span class="fs-5 fw-light">' . e($hero->details) . '</span>';
        }
    }
@endphp

@if ($heroVideo || $heroPoster)
    @include('components.luxury-hero', [
        'heroImage' => $heroPoster,
        'heroVideo' => $heroVideo,
        'title' => $heroTitle,
        'primaryLink' => route('diamonds.index'),
        'primaryLabel' => __('Explore diamonds'),
    ])
@endif
