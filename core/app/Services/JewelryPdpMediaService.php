<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Item;

/**
 * Read-only PDP media helpers (viewer_url, per-metal galleries).
 * Does not affect pricing, cart, or item persistence.
 */
class JewelryPdpMediaService
{
    public const DEFAULT_METAL_KEY = 'YELLOW GOLD';

    private const PREFERRED_METAL_ORDER = ['YELLOW GOLD', 'WHITE GOLD', 'ROSE GOLD'];

    public static function placeholderImageUrl(): string
    {
        return ImageHelper::storageImageUrl(null);
    }

    public static function isEmbedViewer(?string $url): bool
    {
        $lower = strtolower(trim((string) $url));
        if ($lower === '') {
            return false;
        }

        return str_contains($lower, 'ijewel.design/embedded') || str_contains($lower, '/embedded?');
    }

    public static function isImageViewer(?string $url): bool
    {
        $lower = strtolower(trim((string) $url));
        if ($lower === '') {
            return false;
        }

        return (bool) preg_match('/\.(png|jpe?g|gif|webp)(\?.*)?$/i', $lower);
    }

    /**
     * @return array{
     *   url: string,
     *   has_embed: bool,
     *   is_image: bool,
     *   is_fallback_link: bool,
     *   image_src: string|null
     * }
     */
    public static function resolveViewerMeta(Item $item): array
    {
        $item->loadMissing('itemPrice');
        $url = trim((string) ($item->itemPrice->viewer_url ?? ''));
        $hasEmbed = self::isEmbedViewer($url);
        $isImage = self::isImageViewer($url);
        $imageSrc = null;

        if ($isImage && $url !== '' && self::isLikelyImagePath($url)) {
            $imageSrc = self::resolveUrl($url);
        }

        return [
            'url' => $url,
            'has_embed' => $hasEmbed,
            'is_image' => $isImage,
            'is_fallback_link' => $url !== '' && ! $hasEmbed && ! $isImage,
            'image_src' => $imageSrc,
        ];
    }

    /**
     * @return array<string, list<string>> Canonical metal key => resolved image URLs
     */
    public static function metalImageUrlsByKey(Item $item): array
    {
        $item->loadMissing('itemPrice');

        $groups = [
            self::DEFAULT_METAL_KEY => [],
            'WHITE GOLD' => [],
            'ROSE GOLD' => [],
        ];

        $raw = $item->pdp_metal_variants ?? null;
        if (($raw === null || $raw === '') && $item->itemPrice) {
            $raw = $item->itemPrice->image ?? null;
        }

        $decoded = self::decodeRaw($raw);
        if ($decoded === null) {
            return self::filterNonEmptyGroups($groups);
        }

        if (self::looksLikeFlatImageList($decoded)) {
            $urls = self::urlsFromPathList($decoded);
            if ($urls !== []) {
                $groups[self::DEFAULT_METAL_KEY] = $urls;
            }

            return self::filterNonEmptyGroups($groups);
        }

        if (! array_is_list($decoded)) {
            foreach ($decoded as $key => $variant) {
                self::mergeMetalGroup($groups, (string) $key, $variant);
            }

            return self::filterNonEmptyGroups($groups);
        }

        foreach ($decoded as $key => $variant) {
            self::mergeMetalGroup($groups, is_int($key) ? '' : (string) $key, $variant);
        }

        return self::filterNonEmptyGroups($groups);
    }

    /**
     * @param  array<string, list<string>>  $metalImages
     */
    public static function resolveDefaultMetalKey(Item $item, array $metalImages): string
    {
        if (! empty($metalImages[self::DEFAULT_METAL_KEY])) {
            return self::DEFAULT_METAL_KEY;
        }

        foreach (self::PREFERRED_METAL_ORDER as $pref) {
            if (! empty($metalImages[$pref])) {
                return $pref;
            }
        }

        $first = array_key_first($metalImages);

        return $first !== null ? (string) $first : self::DEFAULT_METAL_KEY;
    }

    /**
     * @param  array<string, list<string>>  $metalImages
     */
    public static function showMediaGallery(array $viewerMeta, array $metalImages): bool
    {
        if (! empty($viewerMeta['has_embed'])) {
            return true;
        }

        foreach ($metalImages as $urls) {
            if ($urls !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $sliderImages
     * @param  array<string, list<string>>  $metalImages
     * @return list<string>
     */
    public static function mergeDefaultMetalIntoSlider(
        array $sliderImages,
        array $metalImages,
        string $defaultMetalKey
    ): array {
        $metalUrls = $metalImages[$defaultMetalKey] ?? [];
        $sliderImages = self::sanitizeUrlList($sliderImages);

        if ($metalUrls === []) {
            return $sliderImages;
        }

        return self::sanitizeUrlList(array_merge($metalUrls, $sliderImages));
    }

    /**
     * @param  list<string>  $urls
     * @return list<string>
     */
    public static function sanitizeUrlList(array $urls): array
    {
        $out = [];
        foreach ($urls as $url) {
            $url = trim((string) $url);
            if ($url === '' || ! self::isValidResolvedUrl($url)) {
                continue;
            }
            $out[] = $url;
        }

        return array_values(array_unique($out));
    }

    public static function isValidResolvedUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }
        if (self::isAbsoluteUrl($url)) {
            return (bool) preg_match('#^https?://#i', $url);
        }

        return str_contains($url, '/storage/') || str_contains($url, '/images/');
    }

    /**
     * @param  mixed  $raw
     */
    private static function decodeRaw($raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                if (self::isLikelyImagePath($raw)) {
                    return [self::resolveUrl($raw)];
                }

                return null;
            }

            return $decoded;
        }

        return is_array($raw) ? $raw : null;
    }

    /**
     * @param  array<string, list<string>>  $groups
     * @param  mixed  $variant
     */
    private static function mergeMetalGroup(array &$groups, string $key, $variant): void
    {
        if (is_string($variant) && self::isLikelyImagePath($variant)) {
            $canonical = self::canonicalMetalKey($key !== '' ? $key : self::DEFAULT_METAL_KEY);
            $groups[$canonical] = array_merge($groups[$canonical] ?? [], [self::resolveUrl($variant)]);

            return;
        }

        if (! is_array($variant)) {
            return;
        }

        if (isset($variant['images']) || isset($variant['image']) || isset($variant['photo']) || isset($variant['gallery'])) {
            $metalKey = trim((string) ($variant['key'] ?? $variant['slug'] ?? $variant['label'] ?? $variant['name'] ?? $key));
            if ($metalKey === '') {
                $metalKey = $key !== '' ? $key : self::DEFAULT_METAL_KEY;
            }
            $canonical = self::canonicalMetalKey($metalKey);
            $urls = self::urlsFromPathList(self::pathsFromMetalGroup($variant));
            if ($urls === []) {
                return;
            }
            if (! array_key_exists($canonical, $groups)) {
                $groups[$canonical] = [];
            }
            $groups[$canonical] = array_values(array_unique(array_merge($groups[$canonical], $urls)));

            return;
        }

        if (array_is_list($variant)) {
            $canonical = self::canonicalMetalKey($key !== '' ? $key : self::DEFAULT_METAL_KEY);
            $urls = self::urlsFromPathList($variant);
            if ($urls !== []) {
                $groups[$canonical] = array_values(array_unique(array_merge($groups[$canonical] ?? [], $urls)));
            }
        }
    }

    /**
     * @param  array<string, list<string>>  $groups
     * @return array<string, list<string>>
     */
    private static function filterNonEmptyGroups(array $groups): array
    {
        $out = [];
        foreach (self::PREFERRED_METAL_ORDER as $pref) {
            $sanitized = self::sanitizeUrlList($groups[$pref] ?? []);
            if ($sanitized !== []) {
                $out[$pref] = $sanitized;
            }
        }
        foreach ($groups as $key => $urls) {
            $sanitized = self::sanitizeUrlList($urls);
            if ($sanitized !== [] && ! isset($out[$key])) {
                $out[$key] = $sanitized;
            }
        }

        return $out;
    }

    private static function canonicalMetalKey(string $key): string
    {
        $norm = strtoupper(preg_replace('/\s+/', ' ', trim($key)) ?? '');

        return match ($norm) {
            'YELLOW', 'YELLOW GOLD' => 'YELLOW GOLD',
            'WHITE', 'WHITE GOLD' => 'WHITE GOLD',
            'ROSE', 'ROSE GOLD' => 'ROSE GOLD',
            default => $norm,
        };
    }

    private static function isAbsoluteUrl(string $url): bool
    {
        return (bool) preg_match('#^https?://#i', $url);
    }

    /**
     * Reject product titles / labels mistaken for file paths (e.g. "Celest Veil").
     */
    private static function isLikelyImagePath(string $path): bool
    {
        $path = trim($path);
        if ($path === '') {
            return false;
        }

        if (self::isAbsoluteUrl($path)) {
            return true;
        }

        if (preg_match('#\.(png|jpe?g|gif|webp|svg)(\?|\#|$)#i', $path)) {
            return true;
        }

        if (str_contains($path, '/') || str_contains($path, '\\')) {
            return true;
        }

        return false;
    }

    private static function resolveUrl(string $path): string
    {
        $path = str_replace('items_prices/', '', $path);

        if (! self::isLikelyImagePath($path)) {
            return '';
        }

        $url = self::isAbsoluteUrl($path)
            ? ImageHelper::normalizeStorageImagePath($path)
            : ImageHelper::storageImageUrl($path);

        return self::isValidResolvedUrl($url) ? $url : '';
    }

    /**
     * @return list<string>
     */
    private static function pathsFromMetalGroup(array $metal): array
    {
        $paths = [];

        foreach (['images', 'gallery'] as $listKey) {
            $list = $metal[$listKey] ?? null;
            if (! is_array($list)) {
                continue;
            }
            foreach ($list as $img) {
                $extracted = self::pathFromImageEntry($img);
                if ($extracted !== null) {
                    $paths[] = $extracted;
                }
            }
        }

        foreach (['image', 'photo', 'url', 'src', 'image_url', 'thumb'] as $field) {
            $extracted = self::pathFromImageEntry($metal[$field] ?? null);
            if ($extracted !== null) {
                $paths[] = $extracted;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @param  mixed  $entry
     */
    private static function pathFromImageEntry($entry): ?string
    {
        if (is_string($entry)) {
            return self::isLikelyImagePath($entry) ? trim($entry) : null;
        }

        if (! is_array($entry)) {
            return null;
        }

        foreach (['url', 'src', 'path', 'image', 'photo', 'image_url', 'thumb', 'file'] as $field) {
            $p = trim((string) ($entry[$field] ?? ''));
            if ($p !== '' && self::isLikelyImagePath($p)) {
                return $p;
            }
        }

        return null;
    }

    /**
     * @param  list<mixed>  $paths
     * @return list<string>
     */
    private static function urlsFromPathList(array $paths): array
    {
        $urls = [];
        foreach ($paths as $path) {
            if (is_array($path)) {
                $extracted = self::pathFromImageEntry($path);
                if ($extracted !== null) {
                    $resolved = self::resolveUrl($extracted);
                    if ($resolved !== '') {
                        $urls[] = $resolved;
                    }
                }

                continue;
            }

            $resolved = self::resolveUrl((string) $path);
            if ($resolved !== '') {
                $urls[] = $resolved;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * @param  array<mixed>  $raw
     */
    private static function looksLikeFlatImageList(array $raw): bool
    {
        if (! array_is_list($raw)) {
            return false;
        }

        foreach ($raw as $entry) {
            if (is_array($entry) && (isset($entry['key']) || isset($entry['images']) || isset($entry['image']))) {
                return false;
            }
        }

        return true;
    }
}
