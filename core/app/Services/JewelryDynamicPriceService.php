<?php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Helpers\PriceHelper;
use App\Models\Item;
use App\Models\ItemPrice;

/**
 * Jewelry PDP / cart unit base price from items_prices tier columns
 * (gold_18k_price + vvs_ef_price, etc.).
 * Same units as items.discount_price / #pdp_line_base_price.
 */
class JewelryDynamicPriceService
{
    /**
     * PDP / cart unit base price (same units as items.discount_price).
     * Jewelry with discount_price = 0 uses items_prices tier sum for defaults.
     */
    public static function initialPdpUnitBasePrice(Item $item): float
    {
        if ((float) $item->discount_price > 0) {
            return (float) $item->discount_price;
        }

        $item->loadMissing(['itemPrice', 'diamondAttribute']);
        if (! $item->itemPrice) {
            return (float) $item->discount_price;
        }

        $defaults = self::resolveDefaultSelections($item);
        $resolved = self::resolveUnitBasePriceForSelection(
            $item,
            $defaults['karat'],
            $defaults['color'],
            $defaults['clarity']
        );

        return $resolved ?? 0.0;
    }

    /**
     * Catalog / search / related listing unit base (same units as items.discount_price).
     * Tier jewelry: default PDP combo (18K + VVS/EF when columns exist).
     */
    public static function listingUnitBasePrice(Item $item): float
    {
        if ((float) $item->discount_price > 0) {
            return (float) $item->discount_price;
        }

        if (! self::itemUsesTierPricing($item)) {
            return (float) $item->discount_price;
        }

        $item->loadMissing('itemPrice');
        if ($item->itemPrice) {
            $defaultCombo = self::resolveFromItemPrices($item->itemPrice, '18K', 'VVS / EF');
            if ($defaultCombo !== null && $defaultCombo > 0) {
                return $defaultCombo;
            }
        }

        return self::initialPdpUnitBasePrice($item);
    }

    /**
     * Formatted listing price for grids (does not alter PriceHelper::grandCurrencyPrice()).
     */
    public static function catalogCurrencyPrice(Item $item): string
    {
        if (self::itemUsesTierPricing($item)) {
            $base = self::listingUnitBasePrice($item);
            if ($base > 0) {
                return PriceHelper::setCurrencyPrice($base);
            }
        }

        return PriceHelper::grandCurrencyPrice($item);
    }

    /**
     * @return array{karat: string, color: string, clarity: string}
     */
    public static function resolveDefaultSelections(Item $item): array
    {
        $item->loadMissing(['itemPrice', 'diamondAttribute']);

        $karats = self::normalizeList($item->gold_karat);
        if ($karats === [] && $item->itemPrice) {
            $karats = self::karatsAvailableFromItemPrice($item->itemPrice);
        }

        $da = $item->diamondAttribute;
        $clarity = self::normalizeList($da?->clarity_grade);
        if ($clarity === [] && $item->itemPrice) {
            $clarity = self::clarityTiersAvailableFromItemPrice($item->itemPrice);
        }

        $color = self::normalizeList($da?->color_grade);

        return [
            'karat' => (string) ($karats[0] ?? ''),
            'color' => (string) ($color[0] ?? ''),
            'clarity' => (string) ($clarity[0] ?? ''),
        ];
    }

    /**
     * Unit price from items_prices gold + clarity tier columns.
     *
     * @return float|null Positive unit price in base currency, or null if unavailable.
     */
    public static function resolveUnitBasePriceForSelection(
        Item $item,
        ?string $karat,
        ?string $color,
        ?string $clarity
    ): ?float {
        if ((float) $item->discount_price > 0) {
            return (float) $item->discount_price;
        }

        $item->loadMissing('itemPrice');
        if (! $item->itemPrice) {
            return null;
        }

        return self::resolveFromItemPrices($item->itemPrice, $karat, $clarity);
    }

    /**
     * True when PDP should drive price from items_prices tiers (not fixed discount_price).
     */
    public static function itemUsesTierPricing(Item $item): bool
    {
        if ((float) $item->discount_price > 0) {
            return false;
        }

        return self::clientTierPriceMapForItem($item) !== null;
    }

    /**
     * @deprecated Use itemUsesTierPricing()
     */
    public static function itemUsesDynamicApi(Item $item): bool
    {
        return self::itemUsesTierPricing($item);
    }

    /**
    /**
     * PDP metal image map for trySwapMetalImageByLabel / setPdpGalleryImages.
     * 1. items.pdp_metal_variants  2. items_prices.image
     *
     * @return list<array{key: string, label: string, image: string, images: list<string>}>
     */
    public static function buildMetalVariantMapForItem(Item $item): array
    {
        $item->loadMissing('itemPrice');

        $fromVariants = self::buildMetalVariantMapFromRaw($item->pdp_metal_variants ?? null);
        if ($fromVariants !== []) {
            return $fromVariants;
        }

        if ($item->itemPrice) {
            return self::buildMetalVariantMapFromRaw($item->itemPrice->image ?? null);
        }

        return [];
    }

    /**
     * Tier component prices for PDP client-side sum (gold + clarity).
     *
     * @return array{gold: array<string, float>, clarity: array<string, float>}|null
     */
    public static function clientTierPriceMapForItem(Item $item): ?array
    {
        $item->loadMissing('itemPrice');
        if (! $item->itemPrice) {
            return null;
        }

        $ip = $item->itemPrice;
        $gold = [];
        foreach (['18K' => 'gold_18k_price', '14K' => 'gold_14k_price'] as $label => $column) {
            $v = $ip->{$column} ?? null;
            if (is_numeric($v)) {
                $gold[$label] = (float) $v;
            }
        }

        $clarity = [];
        foreach ([
            'VVS / EF' => 'vvs_ef_price',
            'VVS / GH' => 'vvs_gh_price',
            'VS / GH' => 'vs_gh_price',
            'SI / IJ' => 'si_ij_price',
        ] as $label => $column) {
            $v = $ip->{$column} ?? null;
            if (is_numeric($v)) {
                $clarity[$label] = (float) $v;
            }
        }

        if ($gold === [] && $clarity === []) {
            return null;
        }

        return [
            'gold' => $gold,
            'clarity' => $clarity,
        ];
    }

    /**
     * @deprecated Use clientTierPriceMapForItem()
     */
    public static function clientTierPriceFallbackForItem(Item $item): ?array
    {
        return self::clientTierPriceMapForItem($item);
    }

    public static function resolveFromItemPrices(ItemPrice $itemPrice, ?string $karat, ?string $clarity): ?float
    {
        $total = 0.0;
        $hasComponent = false;

        $goldColumn = self::goldPriceColumn($karat);
        if ($goldColumn !== null) {
            $gold = $itemPrice->{$goldColumn} ?? null;
            if (is_numeric($gold)) {
                $total += (float) $gold;
                $hasComponent = true;
            }
        }

        $clarityColumn = self::clarityPriceColumn($clarity);
        if ($clarityColumn !== null) {
            $clarityPrice = $itemPrice->{$clarityColumn} ?? null;
            if (is_numeric($clarityPrice)) {
                $total += (float) $clarityPrice;
                $hasComponent = true;
            }
        }

        if (! $hasComponent || $total <= 0) {
            return null;
        }

        return round($total, 2);
    }

    /**
     * @return list<string>
     */
    private static function karatsAvailableFromItemPrice(ItemPrice $itemPrice): array
    {
        $out = [];
        if (($itemPrice->gold_18k_price ?? null) !== null) {
            $out[] = '18K';
        }
        if (($itemPrice->gold_14k_price ?? null) !== null) {
            $out[] = '14K';
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function clarityTiersAvailableFromItemPrice(ItemPrice $itemPrice): array
    {
        $tiers = [
            'VVS / EF' => 'vvs_ef_price',
            'VVS / GH' => 'vvs_gh_price',
            'VS / GH' => 'vs_gh_price',
            'SI / IJ' => 'si_ij_price',
        ];
        $out = [];
        foreach ($tiers as $label => $column) {
            if (($itemPrice->{$column} ?? null) !== null) {
                $out[] = $label;
            }
        }

        return $out;
    }

    private static function goldPriceColumn(?string $karat): ?string
    {
        $token = strtoupper(preg_replace('/\s+/', '', (string) $karat) ?? '');
        if ($token === '') {
            return null;
        }
        if (str_contains($token, '18')) {
            return 'gold_18k_price';
        }
        if (str_contains($token, '14')) {
            return 'gold_14k_price';
        }

        return null;
    }

    private static function clarityPriceColumn(?string $clarity): ?string
    {
        $token = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $clarity) ?? '');

        return match (true) {
            str_contains($token, 'VVSEF') => 'vvs_ef_price',
            str_contains($token, 'VVSGH') => 'vvs_gh_price',
            str_contains($token, 'VSGH') => 'vs_gh_price',
            str_contains($token, 'SIIJ') => 'si_ij_price',
            default => null,
        };
    }

    /**
     * @return list<string>
     */
    private static function normalizeList($value): array
    {
        $normalized = Item::normalizeJewelryOptionList($value);

        return is_array($normalized) ? $normalized : [];
    }

    /**
     * @return list<array{key: string, label: string, image: string, images: list<string>}>
     */
    private static function buildMetalVariantMapFromRaw($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return [];
            }
            $raw = $decoded;
        }

        if (! is_array($raw) || $raw === []) {
            return [];
        }

        $rows = array_is_list($raw)
            ? $raw
            : collect($raw)
                ->map(function ($variant, $key) {
                    if (! is_array($variant)) {
                        return ['key' => (string) $key];
                    }

                    if (empty($variant['key'] ?? null) && empty($variant['slug'] ?? null)) {
                        $variant['key'] = (string) $key;
                    }

                    return $variant;
                })
                ->values()
                ->all();

        $preferredOrder = ['YELLOW GOLD', 'WHITE GOLD', 'ROSE GOLD'];
        $found = [];

        foreach ($rows as $variant) {
            if (! is_array($variant)) {
                continue;
            }

            $imagePath = self::variantImagePath($variant);
            if ($imagePath === '') {
                continue;
            }

            $key = trim((string) ($variant['key'] ?? $variant['slug'] ?? $variant['label'] ?? $variant['name'] ?? ''));
            if ($key === '') {
                continue;
            }

            $canonical = strtoupper(preg_replace('/\s+/', ' ', $key));
            if (isset($found[$canonical])) {
                continue;
            }

            $mid = (string) ($variant['key'] ?? $variant['slug'] ?? $key);
            $mlabel = (string) ($variant['label'] ?? $variant['name'] ?? $mid);
            $full = ImageHelper::storageImageUrl($imagePath);

            $imgs = $variant['images'] ?? $variant['gallery'] ?? null;
            $gallery = [];
            if (is_array($imgs)) {
                foreach ($imgs as $gi) {
                    $gi = trim((string) $gi);
                    if ($gi === '') {
                        continue;
                    }
                    $gallery[] = ImageHelper::storageImageUrl($gi);
                }
            }
            if ($gallery === []) {
                $gallery = [$full];
            }

            $found[$canonical] = [
                'key' => $mid,
                'label' => $mlabel,
                'image' => $full,
                'images' => $gallery,
            ];
        }

        if ($found === []) {
            return [];
        }

        $ordered = [];
        foreach ($preferredOrder as $pref) {
            if (isset($found[$pref])) {
                $ordered[] = $found[$pref];
                unset($found[$pref]);
            }
        }
        foreach ($found as $entry) {
            $ordered[] = $entry;
        }

        return $ordered;
    }

    private static function variantImagePath(array $variant): string
    {
        foreach (['image', 'photo', 'url', 'src', 'image_url', 'thumb'] as $field) {
            $path = trim((string) ($variant[$field] ?? ''));
            if ($path !== '') {
                return $path;
            }
        }

        return '';
    }
}
