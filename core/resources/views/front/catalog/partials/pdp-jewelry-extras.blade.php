@php
    $metalVariantMap = \App\Services\JewelryDynamicPriceService::buildMetalVariantMapForItem($item);
@endphp

@once
    <script>
        window.__pdpMetalVariantMap = @json($metalVariantMap);
    </script>
@endonce

{{-- Metal finish chips hidden: we keep pdp_metal_variants as a mapping for Metal Type → image swap. --}}
@php
    $optionList = function ($val) {
        if (is_array($val)) {
            return collect($val)
                ->map(function ($v) { return trim((string) $v); })
                ->filter(function ($v) { return $v !== ''; })
                ->values();
        }

        return collect(explode(',', (string) $val))
            ->map(function ($v) { return trim((string) $v); })
            ->filter(function ($v) { return $v !== ''; })
            ->values();
    };

    $pdpItemPrice = $item->itemPrice;

    $pdpJewelrySourceIsNull = function ($value): bool {
        if ($value === null || $value === '') {
            return true;
        }

        return is_array($value) && $value === [];
    };

    $pdpMetalDisplayLabel = function (string $key): string {
        $norm = strtoupper(preg_replace('/\s+/', ' ', trim($key)) ?? '');

        return match ($norm) {
            'YELLOW GOLD', 'YELLOW' => 'Yellow Gold',
            'WHITE GOLD', 'WHITE' => 'White Gold',
            'ROSE GOLD', 'ROSE' => 'Rose Gold',
            default => ucwords(strtolower(str_replace('_', ' ', trim($key)))),
        };
    };

    $pdpMetalRowsToOptions = function ($raw) use ($pdpMetalDisplayLabel): \Illuminate\Support\Collection {
        if ($raw === null || $raw === '') {
            return collect();
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                return collect();
            }
            $raw = $decoded;
        }

        if (! is_array($raw) || $raw === []) {
            return collect();
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
            $key = trim((string) ($variant['key'] ?? $variant['slug'] ?? $variant['label'] ?? $variant['name'] ?? ''));
            if ($key === '') {
                continue;
            }
            $canonical = strtoupper(preg_replace('/\s+/', ' ', $key));
            if (isset($found[$canonical])) {
                continue;
            }
            $found[$canonical] = $key;
        }

        if ($found === []) {
            return collect();
        }

        $ordered = [];
        foreach ($preferredOrder as $pref) {
            if (isset($found[$pref])) {
                $ordered[] = $found[$pref];
                unset($found[$pref]);
            }
        }
        foreach ($found as $key) {
            $ordered[] = $key;
        }

        return collect($ordered)->map(function (string $key) use ($pdpMetalDisplayLabel) {
            return [
                'value' => $key,
                'label' => $pdpMetalDisplayLabel($key),
            ];
        })->values();
    };

    $pdpMetalsFromVariants = function () use ($item, $pdpMetalRowsToOptions) {
        return $pdpMetalRowsToOptions($item->pdp_metal_variants ?? null);
    };

    $pdpMetalsFromItemPriceImage = function () use ($pdpItemPrice, $pdpMetalRowsToOptions) {
        if (! $pdpItemPrice) {
            return collect();
        }

        return $pdpMetalRowsToOptions($pdpItemPrice->image ?? null);
    };

    $pdpKaratFromItemPrices = function () use ($pdpItemPrice): \Illuminate\Support\Collection {
        if (! $pdpItemPrice) {
            return collect();
        }

        $options = [];
        if (($pdpItemPrice->gold_18k_price ?? null) !== null) {
            $options[] = '18K';
        }
        if (($pdpItemPrice->gold_14k_price ?? null) !== null) {
            $options[] = '14K';
        }

        return collect($options);
    };

    // Material options: items.metal_type → items.pdp_metal_variants → items_prices.image
    $metalTypes = $optionList($item->metal_type ?? '');
    $pdpMetalOptions = collect();

    if ($metalTypes->isNotEmpty()) {
        $pdpMetalOptions = $metalTypes->map(function (string $val) {
            return ['value' => $val, 'label' => $val];
        })->values();
    } else {
        $pdpMetalOptions = $pdpMetalsFromVariants();
        if ($pdpMetalOptions->isEmpty()) {
            $pdpMetalOptions = $pdpMetalsFromItemPriceImage();
        }
        if ($pdpMetalOptions->isNotEmpty()) {
            $metalTypes = $pdpMetalOptions->pluck('value');
        }
    }

    $goldKarats = $optionList($item->gold_karat ?? '');
    if ($pdpJewelrySourceIsNull($item->gold_karat) && $goldKarats->isEmpty()) {
        $goldKarats = $pdpKaratFromItemPrices();
    }

    $pdpClarityFromItemPrices = function () use ($pdpItemPrice): \Illuminate\Support\Collection {
        if (! $pdpItemPrice) {
            return collect();
        }

        $tiers = [
            'VVS / EF' => 'vvs_ef_price',
            'VVS / GH' => 'vvs_gh_price',
            'VS / GH' => 'vs_gh_price',
            'SI / IJ' => 'si_ij_price',
        ];
        $out = [];
        foreach ($tiers as $label => $column) {
            if (($pdpItemPrice->{$column} ?? null) !== null) {
                $out[] = $label;
            }
        }

        return collect($out);
    };

    $da = $item->diamondAttribute ?? null;
    $diamondColorGrades = $optionList($da?->color_grade ?? '');
    $diamondClarityGrades = $optionList($da?->clarity_grade ?? '');
    if ($pdpJewelrySourceIsNull($da?->clarity_grade ?? null) && $diamondClarityGrades->isEmpty()) {
        $diamondClarityGrades = $pdpClarityFromItemPrices();
    }

    // Default selection: first value (or empty). Keeps UI deterministic without affecting cart/pricing.
    $selectedMetal = (string) ($metalTypes->first() ?? '');
    $selectedKarat = (string) ($goldKarats->first() ?? '');
    $selectedDiamondColor = (string) ($diamondColorGrades->first() ?? '');
    $selectedDiamondClarity = (string) ($diamondClarityGrades->first() ?? '');

    $pdpTierPriceMap = \App\Services\JewelryDynamicPriceService::clientTierPriceMapForItem($item);
    $pdpEnableTierPricing = $pdpTierPriceMap !== null
        && ($goldKarats->isNotEmpty() || $diamondClarityGrades->isNotEmpty());

    $pdpKaratBadgePrice = function (string $karat) use ($pdpItemPrice): ?float {
        if (! $pdpItemPrice) {
            return null;
        }
        $token = strtoupper(preg_replace('/\s+/', '', $karat) ?? '');
        if ($token === '') {
            return null;
        }
        if (str_contains($token, '18')) {
            $v = $pdpItemPrice->gold_18k_price ?? null;

            return is_numeric($v) ? (float) $v : null;
        }
        if (str_contains($token, '14')) {
            $v = $pdpItemPrice->gold_14k_price ?? null;

            return is_numeric($v) ? (float) $v : null;
        }

        return null;
    };

    $pdpClarityExtraColumn = function (string $clarity): ?string {
        $token = strtoupper(preg_replace('/[^A-Z0-9]/', '', $clarity) ?? '');

        return match (true) {
            str_contains($token, 'VVSEF') => 'vvs_ef_price',
            str_contains($token, 'VVSGH') => 'vvs_gh_price',
            str_contains($token, 'VSGH') => 'vs_gh_price',
            str_contains($token, 'SIIJ') => 'si_ij_price',
            default => null,
        };
    };

    $pdpClarityExtraAmount = function (string $clarity) use ($pdpItemPrice, $pdpClarityExtraColumn): ?float {
        if (! $pdpItemPrice) {
            return null;
        }
        $column = $pdpClarityExtraColumn($clarity);
        if ($column === null) {
            return null;
        }
        $v = $pdpItemPrice->{$column} ?? null;

        return is_numeric($v) ? (float) $v : null;
    };

    $pdpFormatInrBadge = function (?float $amount): ?string {
        if ($amount === null || $amount <= 0) {
            return null;
        }

        return '₹ ' . number_format($amount, 2);
    };

    $pdpFormatBadgePrice = function (?float $amount): ?string {
        if ($amount === null || $amount <= 0) {
            return null;
        }

        $sign = \App\Helpers\PriceHelper::setCurrencySign();

        return trim($sign) === '₹'
            ? '₹ ' . number_format($amount, 2)
            : $sign . ' ' . number_format($amount, 2);
    };

    $pdpQualityPillLabel = function (string $clarity) use ($pdpFormatInrBadge, $pdpClarityExtraAmount): string {
        $extra = $pdpFormatInrBadge($pdpClarityExtraAmount($clarity));
        if ($extra) {
            return $clarity . ' ( ' . $extra . ' )';
        }

        return $clarity;
    };

    $pdpKaratDisplayLabel = function (string $karat): string {
        return strtolower(trim($karat));
    };

    $pdpGoldWeight = $pdpItemPrice?->gold_weight ?? $item->gold_weight ?? null;
    $pdpLabourPerGram = $pdpItemPrice?->labour_per_gram ?? $item->labour_per_gram ?? null;
    $pdpDiamondShape = trim((string) ($da?->shape ?? ''));
    $pdpDiamondLineParts = [];
    if ($pdpItemPrice && filled($pdpItemPrice->diamond_count ?? null)) {
        $pdpDiamondLineParts[] = $pdpItemPrice->diamond_count . ' ' . __('pcs');
    }
    if ($pdpItemPrice && filled($pdpItemPrice->diamond_weight ?? null)) {
        $pdpDiamondLineParts[] = $pdpItemPrice->diamond_weight . ' ' . __('ct');
    }
    if ($pdpDiamondShape !== '') {
        $pdpDiamondLineParts[] = $pdpDiamondShape;
    }
    $pdpHasSpecs = filled($item->sku)
        || filled($pdpGoldWeight)
        || filled($pdpLabourPerGram)
        || $pdpDiamondLineParts !== [];

    $pdpLineUnitBase = (float) ($pdp_line_unit_base ?? \App\Services\JewelryDynamicPriceService::initialPdpUnitBasePrice($item));
    $pdpMainPriceDisplay = (float) $item->discount_price > 0
        ? PriceHelper::grandCurrencyPrice($item)
        : ($item->itemPrice && $pdpLineUnitBase > 0
            ? PriceHelper::setCurrencyPrice($pdpLineUnitBase)
            : PriceHelper::grandCurrencyPrice($item));
$pdpMakingCharge = null;

if (!empty($item->details)) {
    preg_match('/Making Charge:\s*(.*?)Diamonds:/is', strip_tags($item->details), $matches);
    $pdpMakingCharge = trim($matches[1] ?? '');
}
@endphp

<div class="ij-pdp-configurator luxury-pdp-configurator" data-ij-pdp-config>
    @if ($pdpHasSpecs)
        <div class="ij-pdp-specs">
            @if (filled($item->sku))
                <p class="ij-pdp-specs__line">
                    <span class="ij-pdp-specs__label">{{ __('Product code') }}:</span>
                    <span class="ij-pdp-specs__value">{{ $item->sku }}</span>
                </p>
            @endif
            @if (filled($pdpGoldWeight))
                <p class="ij-pdp-specs__line">
                    <span class="ij-pdp-specs__label">{{ __('Gold Weight') }}:</span>
                    <span class="ij-pdp-specs__value">{{ number_format((float) $pdpGoldWeight, 2) }}g</span>
                </p>
            @endif
{{--            @if (filled($pdpLabourPerGram))--}}
{{--                <p class="ij-pdp-specs__line">--}}
{{--                    <span class="ij-pdp-specs__label">{{ __('Making Charge') }}:</span>--}}
{{--                    <span class="ij-pdp-specs__value">{{ $pdpLabourPerGram }}</span>--}}
{{--                </p>--}}
{{--            @endif--}}
                @if (filled($pdpMakingCharge))
                    <p class="ij-pdp-specs__line">
                        <span class="ij-pdp-specs__label">{{ __('Making Charge') }}:</span>
                        <span class="ij-pdp-specs__value">{{ $pdpMakingCharge }}</span>
                    </p>
                @endif
            @if ($pdpDiamondLineParts !== [])
                <p class="ij-pdp-specs__line">
                    <span class="ij-pdp-specs__label">{{ __('Diamonds') }}:</span>
                    <span class="ij-pdp-specs__value">{{ implode(', ', $pdpDiamondLineParts) }}</span>
                </p>
            @endif
        </div>
    @endif

    <div class="ij-pdp-price luxury-pdp-price-block">
        <div class="ij-pdp-price__inner price-area" id="pdp_price_area"
            data-initial-previous="{{ (float) $item->previous_price }}"
            data-initial-discount="{{ (float) $item->discount_price }}">
            <small id="pdp_compare_price_wrap"
                class="ij-pdp-price__compare @if ($item->previous_price == 0) d-none @endif">
                <del id="pdp_compare_price">{{ $item->previous_price != 0 ? PriceHelper::setPreviousPrice($item->previous_price) : '' }}</del>
            </small>
            <span id="main_price" class="main-price product-price ij-pdp-price__amount">{{ $pdpMainPriceDisplay }}</span>
        </div>
    </div>

    <input type="hidden" id="pdp_selected_metal_type" value="{{ $selectedMetal }}">
    <input type="hidden" id="pdp_selected_gold_karat" value="{{ $selectedKarat }}">
    <input type="hidden" id="pdp_selected_diamond_color" value="{{ $selectedDiamondColor }}">
    <input type="hidden" id="pdp_selected_diamond_clarity" value="{{ $selectedDiamondClarity }}">

    <div class="ij-pdp-options">
        @if ($pdpMetalOptions->isNotEmpty())
            <div class="ij-pdp-field">
                <div class="ij-pdp-field__title">{{ __('Material') }}</div>
                <div class="ij-pdp-field__row ij-pdp-field__row--material option-group" data-option-group="metal_type">
                    @foreach ($pdpMetalOptions as $opt)
                        @php
                            $metalVal = (string) ($opt['value'] ?? '');
                            $metalLabel = (string) ($opt['label'] ?? $metalVal);
                            $metalActive = strtolower($selectedMetal) === strtolower($metalVal);
                        @endphp
                        <label class="ij-pdp-radio">
                            <button
                                type="button"
                                class="option-btn metal-btn ij-pdp-radio__btn {{ $metalActive ? 'active' : '' }}"
                                data-value="{{ $metalVal }}"
                                data-metal-key="{{ $metalVal }}">
                                <span class="ij-pdp-radio__control" aria-hidden="true"></span>
                                <span class="ij-pdp-radio__text">{{ $metalLabel }}</span>
                            </button>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($goldKarats->isNotEmpty())
            <div class="ij-pdp-field">
                <div class="ij-pdp-field__title">{{ __('Gold Carat') }}</div>
                <div class="ij-pdp-field__row ij-pdp-field__row--karat option-group" data-option-group="gold_karat">
                    @foreach ($goldKarats as $val)
                        @php
                            $karatBadge = $pdpFormatInrBadge($pdpKaratBadgePrice($val));
                            $karatActive = strtoupper($selectedKarat) === strtoupper($val);
                        @endphp
                        <label class="ij-pdp-radio ij-pdp-radio--karat">
                            <button
                                type="button"
                                class="option-btn karat-btn ij-pdp-radio__btn {{ $karatActive ? 'active' : '' }}"
                                data-value="{{ $val }}">
                                <span class="ij-pdp-radio__control" aria-hidden="true"></span>
                                <span class="ij-pdp-radio__text ij-pdp-radio__text--karat">{{ $pdpKaratDisplayLabel($val) }}</span>
                                @if ($karatBadge)
                                    <span class="ij-pdp-karat-badge">{{ $karatBadge }}</span>
                                @endif
                            </button>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($diamondClarityGrades->isNotEmpty())
            <div class="ij-pdp-field">
                <div class="ij-pdp-field__title">{{ __('Quality') }}</div>
                <div class="ij-pdp-field__row ij-pdp-field__row--quality option-group" data-option-group="diamond_clarity">
                    @foreach ($diamondClarityGrades as $val)
                        @php $clarityActive = strtoupper($selectedDiamondClarity) === strtoupper($val); @endphp
                        <label class="ij-pdp-pill">
                            <button
                                type="button"
                                class="option-btn clarity-btn ij-pdp-pill__btn {{ $clarityActive ? 'active' : '' }}"
                                data-value="{{ $val }}">
                                <span class="ij-pdp-pill__label">{{ $pdpQualityPillLabel($val) }}</span>
                            </button>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($diamondColorGrades->isNotEmpty())
            <div class="ij-pdp-field ij-pdp-field--extra">
                <div class="ij-pdp-field__title">{{ __('Diamond Color') }}</div>
                <div class="ij-pdp-field__row ij-pdp-field__row--quality option-group" data-option-group="diamond_color">
                    @foreach ($diamondColorGrades as $val)
                        @php $colorActive = strtoupper($selectedDiamondColor) === strtoupper($val); @endphp
                        <label class="ij-pdp-pill">
                            <button
                                type="button"
                                class="option-btn color-btn ij-pdp-pill__btn {{ $colorActive ? 'active' : '' }}"
                                data-value="{{ $val }}">
                                <span class="ij-pdp-pill__label">{{ $val }}</span>
                            </button>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@once
  <script>
    (function () {
      function setMainPdpImageSrc(src) {
        if (!src) return;

        var stageImg = document.querySelector('[data-product-media] [data-media-image]');
        if (stageImg) {
          stageImg.src = src;
          var wrap = stageImg.closest('[data-media-image-wrap]');
          if (wrap) wrap.classList.remove('d-none');
        }

        var activeImg = document.querySelector('#productGallery .product-details-slider .owl-item.active img');
        if (activeImg) {
          activeImg.src = src;
          return;
        }

        var firstImg = document.querySelector('#productGallery .product-details-slider .item:first-child img');
        if (firstImg) firstImg.src = src;
      }

      function setPdpGalleryImages(imgList) {
        // Replace the entire PDP gallery (main slider + thumbs) for metal-specific galleries.
        // Does not touch any pricing/cart logic.
        if (!Array.isArray(imgList) || imgList.length === 0) return false;

        var gallery = document.querySelector('#productGallery .product-details-slider');
        if (!gallery) return false;

        // Legacy vertical rail only when iJewel metal strip is not active.
        var ijActive = document.querySelector('[data-product-media].pdp-product-media--ij-active');
        var legacyThumbs = !ijActive
          ? (document.querySelector('[data-lux-thumbs]') || document.querySelector('.gallery-thumbs'))
          : null;
        if (legacyThumbs) {
          legacyThumbs.innerHTML = imgList.map(function (u) {
            return '<button type="button" class="lux-thumb" data-lux-thumb><img src="' + String(u) + '" class="gallery-thumb" loading="lazy" alt="" decoding="async"></button>';
          }).join('');
        }

        var luxGallery = document.querySelector('#productGallery[data-lux-gallery]');
        var useZoom = !!luxGallery;

        function owlItemHtml(u) {
          if (useZoom) {
            return '<div class="item"><div class="lux-zoom-wrap" data-lux-zoom><img src="' + String(u) + '" loading="lazy" alt="" class="lux-main-img"></div></div>';
          }
          return '<div class="item"><img src="' + String(u) + '" loading="lazy" alt=""></div>';
        }

        // If OwlCarousel is initialized, rebuild via jQuery API (safe refresh).
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.owlCarousel) {
          var $ = window.jQuery;
          var $owl = $(gallery);
          if ($owl.hasClass('owl-loaded')) {
            $owl.trigger('replace.owl.carousel', [imgList.map(owlItemHtml).join('')]);
            $owl.trigger('refresh.owl.carousel');
            if (typeof window.__luxPdpGalleryInit === 'function') {
              window.__luxPdpGalleryInit();
            }
            return true;
          }
        }

        // Non-owl fallback: replace markup.
        gallery.innerHTML = imgList.map(owlItemHtml).join('');
        if (typeof window.__luxPdpGalleryInit === 'function') {
          window.__luxPdpGalleryInit();
        }
        return true;
      }

      function normalizeMetalToken(str) {
        return String(str || '')
          .toLowerCase()
          .replace(/[\s_-]+/g, ' ')
          .trim();
      }

      function trySwapMetalImageByLabel(label) {
        if (window.PdpMedia && typeof window.PdpMedia.renderMetalByLabel === 'function') {
          window.PdpMedia.renderMetalByLabel(label);
          return;
        }

        var desired = normalizeMetalToken(label);
        if (!desired) return;

        var raw = window.__pdpMetalVariantMap || [];
        if (!Array.isArray(raw) || raw.length === 0) return;
        for (var j = 0; j < raw.length; j++) {
          var v = raw[j] || {};
          var vKey = normalizeMetalToken(v.key || v.slug || '');
          var vLabel = normalizeMetalToken(v.label || v.name || '');
          if (desired === vKey || desired === vLabel) {
            var src = v.image || v.photo || '';
            var list = Array.isArray(v.images) && v.images.length ? v.images : null;
            if (list) {
              setPdpGalleryImages(list);
            } else if (src) {
              setMainPdpImageSrc(src);
            }
            return;
          }
        }
      }

      window.__pdpSetGalleryImages = setPdpGalleryImages;
      window.__pdpSetMainImageSrc = setMainPdpImageSrc;

      function resolveOptionButton(target, group) {
        if (!target || !group) return null;
        var btn = target.closest ? target.closest('.option-btn') : null;
        if (btn && group.contains(btn)) return btn;
        var label = target.closest ? target.closest('label') : null;
        if (label && group.contains(label)) {
          btn = label.querySelector('.option-btn');
          if (btn) return btn;
        }
        return null;
      }

      document.addEventListener('click', function (e) {
        var group = e.target && e.target.closest ? e.target.closest('.option-group') : null;
        if (!group) return;

        var btn = resolveOptionButton(e.target, group);
        if (!btn) return;

        group.querySelectorAll('.option-btn').forEach(function (b) {
          b.classList.remove('active');
        });
        btn.classList.add('active');

        var gv = group.getAttribute('data-option-group') || '';
        var v = btn.getAttribute('data-value') || btn.textContent || '';
        v = String(v).trim();
        if (gv === 'metal_type') {
          var el = document.getElementById('pdp_selected_metal_type');
          if (el) el.value = v;
          trySwapMetalImageByLabel(v);
        } else if (gv === 'gold_karat') {
          var el2 = document.getElementById('pdp_selected_gold_karat');
          if (el2) el2.value = v;
        } else if (gv === 'diamond_color') {
          var el3 = document.getElementById('pdp_selected_diamond_color');
          if (el3) el3.value = v;
        } else if (gv === 'diamond_clarity') {
          var el4 = document.getElementById('pdp_selected_diamond_clarity');
          if (el4) el4.value = v;
        }

        if (gv === 'gold_karat' || gv === 'diamond_color' || gv === 'diamond_clarity') {
          document.dispatchEvent(new CustomEvent('pdp-jewelry-variant-change', { bubbles: true }));
        }
      });
    })();
  </script>
@endonce

{{-- items_prices tier pricing (gold_* + vvs_* / vs_* / si_*); no external API. --}}
@if (! empty($pdpEnableTierPricing))
<script>
(function () {
  window.__pdpTierPriceMap = @json($pdpTierPriceMap);

  function pdpNumberFormat(number, decimals, decPoint, thousandsSep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number;
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    var sep = thousandsSep === undefined ? ',' : thousandsSep;
    var dec = decPoint === undefined ? '.' : decPoint;
    var toFixedFix = function (num, prec2) {
      var k = Math.pow(10, prec2);
      return '' + Math.round(num * k) / k;
    };
    var s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
  }

  function getCurrencyParts() {
    var signEl = document.getElementById('set_currency');
    var dirEl = document.getElementById('currency_direction');
    var valEl = document.getElementById('set_currency_val');
    return {
      sign: signEl ? signEl.value : '',
      direction: dirEl ? String(dirEl.value) : '0',
      value: valEl ? parseFloat(valEl.value) || 1 : 1,
    };
  }

  function formatMoneyDisplay(basePrice) {
    var c = getCurrencyParts();
    var conv = Math.round(parseFloat(basePrice) * c.value * 100) / 100;
    if (!isFinite(conv)) return '';
    var dec = typeof decimal_separator !== 'undefined' ? decimal_separator : '.';
    var thou = typeof thousand_separator !== 'undefined' ? thousand_separator : ',';
    var formatted = pdpNumberFormat(conv, 2, dec, thou);
    if (c.direction === '1' || c.direction === 1) {
      return c.sign + formatted;
    }
    return formatted + c.sign;
  }

  function convertBaseToSession(basePrice) {
    var v = getCurrencyParts().value;
    var n = Math.round(parseFloat(basePrice) * v * 100) / 100;
    return isFinite(n) ? n : null;
  }

  function normalizeKaratToken(str) {
    return String(str || '').toUpperCase().replace(/\s+/g, '');
  }

  function normalizeClarityToken(str) {
    return String(str || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
  }

    function resolvePdpTierPrice() {
        var tiers = window.__pdpTierPriceMap;
        if (!tiers) return null;

        var kEl = document.getElementById('pdp_selected_gold_karat');
        var qEl = document.getElementById('pdp_selected_diamond_clarity');

        var kToken = normalizeKaratToken(kEl ? String(kEl.value || '').trim() : '');
        var qToken = normalizeClarityToken(qEl ? String(qEl.value || '').trim() : '');

        var qualityPrice = 0;
        var goldPrice = 0;

        // Gold Karat Price
        var goldKey = null;
        if (kToken.indexOf('18') >= 0) goldKey = '18K';
        else if (kToken.indexOf('14') >= 0) goldKey = '14K';

        if (goldKey && tiers.gold && tiers.gold[goldKey] != null) {
            goldPrice = parseFloat(tiers.gold[goldKey]) || 0;
        }

        // Diamond Quality Price
        var clarityKey = null;
        if (qToken.indexOf('VVSEF') >= 0) clarityKey = 'VVS / EF';
        else if (qToken.indexOf('VVSGH') >= 0) clarityKey = 'VVS / GH';
        else if (qToken.indexOf('VSGH') >= 0) clarityKey = 'VS / GH';
        else if (qToken.indexOf('SIIJ') >= 0) clarityKey = 'SI / IJ';

        if (clarityKey && tiers.clarity && tiers.clarity[clarityKey] != null) {
            qualityPrice = parseFloat(tiers.clarity[clarityKey]) || 0;
        }

        // Gold Weight from blade
        var goldWeight = {{ (float) ($pdpGoldWeight ?? 0) }};

        // Gold Rate per gram
        var goldRate = 1800;

        // Gold Amount
        var goldAmount = goldWeight * goldRate;

        // Subtotal
        var subtotal = qualityPrice + goldPrice + goldAmount;

        // 10% Margin
        var finalPrice = subtotal * 1.10;

        return Math.round(finalPrice);
    }

  function applyPdpTierPrice() {
    var base = resolvePdpTierPrice();
    if (base == null) return;

    var demo = document.getElementById('demo_price');
    var main = document.getElementById('main_price');
    var conv = convertBaseToSession(base);
    if (conv == null) return;
    var display = formatMoneyDisplay(base);
    if (demo) demo.value = String(conv);
    var lineBase = document.getElementById('pdp_line_base_price');
    if (lineBase) lineBase.value = String(base);
    if (main) main.textContent = display;
    document.querySelectorAll('.product-price').forEach(function (el) {
      el.textContent = display;
    });

    var wrap = document.getElementById('pdp_compare_price_wrap');
    if (wrap) wrap.classList.add('d-none');

    if (window.jQuery) {
      var $qty = window.jQuery('.details-page-top-right-content .qtyValue');
      if ($qty.length) {
        $qty.trigger('keyup');
      }
    }
  }

  document.addEventListener('pdp-jewelry-variant-change', applyPdpTierPrice);
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyPdpTierPrice);
  } else {
    applyPdpTierPrice();
  }
})();
</script>
@endif
<div class="mb-3 pdp-ring-size-input">
    <label class="filter-label mb-1">{{ __('Ring size') }}</label>
    <input type="text"
           id="pdp_ring_size"
           class="form-control form-control-sm"
           placeholder="{{ __('Enter your size (e.g. 6, 7, 8)') }}"
           inputmode="decimal"
           autocomplete="off">
    <small class="text-muted d-block mt-1">
        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#ringSizerModal">
            {{ __('Find your ring size') }}
        </a>
    </small>
</div>
@if ($show_pdp_engraving ?? false)
    <div class="mb-3 pdp-engrave luxury-pdp-engrave">
        {{-- Serialized engraving for existing cart AJAX (myscript.js reads #pdp_engraving) --}}
        <input type="hidden" id="pdp_engraving" value="" autocomplete="off">
        <p class="luxury-pdp-engrave__kicker mb-2">{{ __('Complimentary engraving (optional)') }}</p>

       
        <div class="engraving-section" id="luxury-engraving-section">
            <div class="engraving-toggle">
                <label class="engraving-toggle__label">
                    <input type="checkbox" id="luxury-engraving-checkbox" class="engraving-toggle__checkbox">
                    <span class="engraving-toggle__text">{{ __('✦ Add Engraving (Free)') }}</span>
                </label>
            </div>

            <div class="engraving-fields" id="luxury-engraving-fields" style="display: none;">

                <div class="engraving-field">
                    <label for="luxury-engraving-text">{{ __('Engraving text') }}</label>
                    <input type="text" id="luxury-engraving-text" maxlength="20"
                        placeholder="{{ __('Enter text (max 20 characters)') }}" class="engraving-input"
                        autocomplete="off">
                    <small class="engraving-char-count"><span id="luxury-engraving-char-count">0</span>/20
                        {{ __('characters') }}</small>
                </div>

                <div class="engraving-field">
                    <label for="luxury-engraving-font">{{ __('Font style') }}</label>
                    <select id="luxury-engraving-font" class="engraving-select">
                        <option value="">-- {{ __('Select font') }} --</option>
                        <option value="Classic Serif">{{ __('Classic Serif') }}</option>
                        <option value="Modern Script">{{ __('Modern Script') }}</option>
                        <option value="Block Print">{{ __('Block Print') }}</option>
                        <option value="Diamond Cut">{{ __('Diamond Cut') }}</option>
                    </select>
                </div>

                <div class="engraving-field">
                    <label for="luxury-engraving-placement">{{ __('Placement') }}</label>
                    <select id="luxury-engraving-placement" class="engraving-select">
                        <option value="">-- {{ __('Select placement') }} --</option>
                        <option value="Inside Band">{{ __('Inside band') }}</option>
                        <option value="Outside Band">{{ __('Outside band') }}</option>
                        <option value="Inside Bracelet">{{ __('Inside bracelet') }}</option>
                    </select>
                </div>

                <div class="engraving-field">
                    <label for="luxury-engraving-logo">{{ __('Upload logo / image (optional)') }}</label>
                    <input type="file" id="luxury-engraving-logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                        class="engraving-file-input">
                    <small class="engraving-char-count">{{ __('Accepted formats: JPG, PNG, SVG (max 2MB)') }}</small>

                    <div class="engraving-logo-preview" id="luxury-engraving-logo-preview-wrap" style="display:none;">
                        <p class="engraving-preview__label">{{ __('Logo preview') }}</p>
                        <img id="luxury-engraving-logo-preview-img" src="" alt="{{ __('Logo preview') }}"
                            style="max-width:120px; max-height:80px;">
                    </div>

                    <input type="hidden" id="luxury-engraving-logo-name" value="">
                </div>

                <div class="engraving-preview" id="luxury-engraving-preview">
                    <p class="engraving-preview__label">{{ __('Preview') }}</p>
                    <p class="engraving-preview__text" id="luxury-engraving-preview-text">{{ __('Your text here') }}</p>
                </div>
            </div>
        </div>
    </div>

    @php
        $luxuryPdpEngraveLabels = [
            'text' => __('Text'),
            'font' => __('Font'),
            'placement' => __('Placement'),
            'logoFile' => __('Logo file'),
            'alertTooLarge' => __('File is too large. Please upload an image under 2MB.'),
        ];
    @endphp

    <script>
        (function() {
            var L = @json($luxuryPdpEngraveLabels);
            var checkbox = document.getElementById('luxury-engraving-checkbox');
            var fields = document.getElementById('luxury-engraving-fields');
            var hidden = document.getElementById('pdp_engraving');
            var textInput = document.getElementById('luxury-engraving-text');
            var fontSelect = document.getElementById('luxury-engraving-font');
            var placementSelect = document.getElementById('luxury-engraving-placement');
            var charCountEl = document.getElementById('luxury-engraving-char-count');
            var previewText = document.getElementById('luxury-engraving-preview-text');
            var logoInput = document.getElementById('luxury-engraving-logo');
            var logoPreviewWrap = document.getElementById('luxury-engraving-logo-preview-wrap');
            var logoPreviewImg = document.getElementById('luxury-engraving-logo-preview-img');
            var logoNameInput = document.getElementById('luxury-engraving-logo-name');

            if (!checkbox || !fields || !hidden || !textInput || !fontSelect || !placementSelect || !previewText) {
                return;
            }

            var fallbackPreview = previewText.textContent || 'Your text here';

            function buildCartString() {
                if (!checkbox.checked) {
                    return '';
                }
                var text = (textInput.value || '').trim();
                var font = fontSelect.value || '';
                var placement = placementSelect.value || '';
                var logo = (logoNameInput && logoNameInput.value) ? String(logoNameInput.value).trim() : '';
                var parts = [];
                if (text) {
                    parts.push(L.text + ': ' + text);
                }
                if (font) {
                    parts.push(L.font + ': ' + font);
                }
                if (placement) {
                    parts.push(L.placement + ': ' + placement);
                }
                if (logo) {
                    parts.push(L.logoFile + ': ' + logo);
                }
                return parts.join(' | ');
            }

            function syncHidden() {
                hidden.value = buildCartString();
            }

            function applyFontPreview() {
                var fontMap = {
                    'Classic Serif': 'Georgia, "Times New Roman", serif',
                    'Modern Script': 'cursive, "Brush Script MT", serif',
                    'Block Print': 'ui-monospace, monospace',
                    'Diamond Cut': 'Copperplate, fantasy, serif'
                };
                previewText.style.fontFamily = fontMap[fontSelect.value] || 'inherit';
            }

            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    fields.style.display = 'block';
                    syncHidden();
                    applyFontPreview();
                } else {
                    fields.style.display = 'none';
                    textInput.value = '';
                    fontSelect.value = '';
                    placementSelect.value = '';
                    if (logoInput) logoInput.value = '';
                    if (logoNameInput) logoNameInput.value = '';
                    if (logoPreviewWrap) logoPreviewWrap.style.display = 'none';
                    if (logoPreviewImg) logoPreviewImg.removeAttribute('src');
                    previewText.textContent = fallbackPreview;
                    if (charCountEl) charCountEl.textContent = '0';
                    previewText.style.fontFamily = 'inherit';
                    syncHidden();
                }
            });

            textInput.addEventListener('input', function() {
                var c = this.value.length;
                if (charCountEl) charCountEl.textContent = String(c);
                previewText.textContent = this.value.trim() ? this.value : fallbackPreview;
                syncHidden();
            });

            fontSelect.addEventListener('change', function() {
                applyFontPreview();
                syncHidden();
            });

            placementSelect.addEventListener('change', syncHidden);

            if (logoInput && logoPreviewWrap && logoPreviewImg && logoNameInput) {
                logoInput.addEventListener('change', function() {
                    var file = this.files && this.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            alert(L.alertTooLarge);
                            this.value = '';
                            logoNameInput.value = '';
                            logoPreviewWrap.style.display = 'none';
                            logoPreviewImg.removeAttribute('src');
                            syncHidden();
                            return;
                        }
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            logoPreviewImg.src = e.target.result || '';
                            logoPreviewWrap.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                        logoNameInput.value = file.name || '';
                        syncHidden();
                    } else {
                        logoPreviewWrap.style.display = 'none';
                        logoNameInput.value = '';
                        logoPreviewImg.removeAttribute('src');
                        syncHidden();
                    }
                });
            }

            syncHidden();
        })();
    </script>
@endif

@once
    <script>
        function swapPdpMetalImage(btn) {
            var key = btn.getAttribute('data-metal-key') || '';
            var src = btn.getAttribute('data-metal-img');
            if (!src) return;
            var box = document.getElementById('pdpMetalSelector');
            if (box) {
                box.querySelectorAll('.metal-chip').forEach(function (b) {
                    b.classList.remove('metal-chip--active');
                });
            }
            btn.classList.add('metal-chip--active');
            var hidden = document.getElementById('pdp_selected_metal');
            if (hidden) hidden.value = key || '';
            // Swap ONLY the main PDP image (first slide). Do not change thumbnails/secondary gallery.
            setMainPdpImageSrc(src);
        }
    </script>
@endonce
