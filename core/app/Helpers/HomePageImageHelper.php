<?php

namespace App\Helpers;

class HomePageImageHelper
{
    public static function exists(?string $relativePath): bool
    {
        $relativePath = ltrim(trim((string) $relativePath), '/');
        if ($relativePath === '') {
            return false;
        }

        return is_file(public_path('storage/images/' . $relativePath));
    }

    public static function url(?string $relativePath): ?string
    {
        $relativePath = ltrim(trim((string) $relativePath), '/');
        if ($relativePath === '' || ! self::exists($relativePath)) {
            return null;
        }

        return url('/core/public/storage/images/' . implode('/', array_map('rawurlencode', explode('/', $relativePath))));
    }

    /**
     * @param  string|array<int, string>|null  ...$candidates
     */
    public static function resolveUrl(...$candidates): string
    {
        $flat = [];
        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                foreach ($candidate as $item) {
                    $flat[] = $item;
                }
            } else {
                $flat[] = $candidate;
            }
        }

        foreach ($flat as $relative) {
            if ($url = self::url($relative)) {
                return $url;
            }
        }

        return ImageHelper::storageImageUrl(null);
    }

    public static function resolveFromAdmin(?string $adminPath, array $fallbacks): string
    {
        $adminPath = ltrim(trim((string) $adminPath), '/');
        if ($adminPath !== '' && ($url = self::url($adminPath))) {
            return $url;
        }

        return self::resolveUrl($fallbacks);
    }

    public static function africanCaratUrl(int $number): string
    {
        $paths = config('home_page_images.african_carat', []);

        return self::resolveUrl($paths[$number] ?? "African carat/{$number}.png");
    }

    public static function trustUrl(string $key): string
    {
        $fallbacks = config('home_page_images.trust.' . $key, []);

        return self::resolveUrl($fallbacks);
    }

    public static function inspectionUrl(): string
    {
        return self::resolveUrl(config('home_page_images.inspection', []));
    }

    public static function diamondShapeUrl(string $shape): string
    {
        $fallbacks = config('home_page_images.diamond_shapes.' . $shape, []);

        return self::resolveUrl($fallbacks);
    }

    public static function crowningUrl(string $key): string
    {
        $fallbacks = config('home_page_images.crowning.' . $key, []);

        return self::resolveUrl($fallbacks);
    }

    public static function crowningVideoUrl(string $key): ?string
    {
        foreach (config('home_page_images.crowning_video.' . $key, []) as $relative) {
            if ($url = self::url($relative)) {
                return $url;
            }
        }

        return null;
    }

    public static function reimaginedBannerUrl(): string
    {
        return self::resolveUrl(config('home_page_images.reimagined_banner', []));
    }

    public static function bannerThirdUrl(int $slot): string
    {
        $fallbacks = config('home_page_images.banner_third.' . $slot, []);

        return self::resolveUrl($fallbacks);
    }

    public static function bannerSecondRowUrl(int $slot): string
    {
        $fallbacks = config('home_page_images.banner_second_row.' . $slot, []);

        return self::resolveUrl($fallbacks);
    }

    public static function serviceUrl(?string $adminPhoto, int $index = 0): string
    {
        $cycle = config('home_page_images.services_cycle', []);
        if ($cycle === []) {
            return self::resolveFromAdmin($adminPhoto, ['placeholder.png']);
        }

        $fallbacks = [];
        $count = count($cycle);
        for ($i = 0; $i < $count; $i++) {
            $fallbacks[] = $cycle[($index + $i) % $count];
        }

        return self::resolveFromAdmin($adminPhoto, $fallbacks);
    }

    public static function blogPostUrl(?string $photoField): string
    {
        if ($photoField) {
            $decoded = json_decode($photoField, true);
            if (is_array($decoded) && $decoded !== []) {
                $first = $decoded[array_key_first($decoded)] ?? null;
                if ($first && ($url = self::url((string) $first))) {
                    return $url;
                }
            }
            if ($url = self::url($photoField)) {
                return $url;
            }
        }

        return self::resolveUrl(config('home_page_images.blog_fallback', []));
    }

    public static function brandUrl(?string $adminPhoto): string
    {
        return self::resolveFromAdmin($adminPhoto, config('home_page_images.brand_fallback', []));
    }

    /**
     * Trending Picks card image: cycle from config (storage/images paths) when file exists, else product thumb.
     */
    public static function trendingPickUrl(?string $productPhoto, int $index = 0): string
    {
        $cycle = config('home_page_images.trending_picks_cycle', []);
        if ($cycle !== []) {
            $relative = $cycle[$index % count($cycle)] ?? null;
            if ($relative && self::exists($relative)) {
                return (string) self::url($relative);
            }
        }

        $productPath = ltrim(trim((string) $productPhoto), '/');
        if ($productPath !== '' && $productPath !== 'placeholder.png' && self::exists($productPath)) {
            return ImageHelper::storageImageUrl($productPath);
        }

        return self::resolveUrl($cycle !== [] ? $cycle : ['placeholder.png']);
    }

    public static function theme4FeaturedUrl(?string $adminImg, int $slot): string
    {
        $african = config('home_page_images.african_carat.' . $slot);
        $row = config('home_page_images.banner_second_row.' . $slot, []);
        $rowFirst = is_array($row) ? ($row[0] ?? null) : $row;

        $extra = [];
        if ($slot === 3) {
            $extra[] = 'WUu8diamond-rings-with-high-quality-size-1920-1080.png';
            $extra[] = 'TBGVBlack and White Elegant Diamonds Instagram Post (1).jpg';
        }

        return self::resolveFromAdmin($adminImg, array_values(array_filter(array_merge(
            $extra,
            [$african, $rowFirst, 'African carat/' . $slot . '.png']
        ))));
    }
}
