<?php

namespace App\Repositories\Front;

use App\{
    Models\Cart,
    Models\Item,
    Models\PromoCode,
    Helpers\PriceHelper
};
use App\Helpers\ImageHelper;
use App\Services\JewelryDynamicPriceService;
use App\Models\AttributeOption;
use App\Models\Attribute;
use Illuminate\Support\Facades\Session;

class CartRepository
{

    /**
     * Store cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store($request)
    {

        if (empty($request->all())) {
            $parsedUrl = parse_url($request->getRequestUri(), PHP_URL_QUERY); // Extracts the query part
            parse_str($parsedUrl, $queryArray);
            $request = (object)$queryArray;
            $qty_check  = 0;
            $input = $queryArray;
        } else {
            $input = $request->all();
        }

        $input['options_ids'] = $input['options_ids'] ?? '';
        $input['attribute_ids'] = $input['attribute_ids'] ?? '';

        $qty_check  = 0;

        $input['option_name'] = [];
        $input['option_price'] = [];
        $input['attr_name'] = [];

        $isIncrement = isset($input['isIncrement']) ? (string) $input['isIncrement'] : '';

        $qty = isset($input['quantity']) ? $input['quantity'] : 1;
        $qty = is_numeric($qty) ? $qty : 1;


        if ($input['options_ids']) {
            foreach (explode(',', $input['options_ids']) as $optionId) {
                $option = AttributeOption::findOrFail($optionId);
                if ($qty > $option->stock) {
                    $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                    return $data;
                }
            }
        }

        $cart = Session::get('cart');

        $item = Item::where('id', $input['item_id'])
            ->select('id', 'name', 'photo', 'discount_price', 'previous_price', 'slug', 'item_type', 'license_name', 'license_key', 'stock', 'metal_type', 'gold_karat', 'pdp_metal_variants')
            ->first();

        if ($item->item_type == 'normal') {
            $checkQty = isset($input['quantity']) ? $input['quantity'] : (isset($request->quantity) ? $request->quantity : 1);
            if ($item->stock < $checkQty) {
                $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                return $data;
            }
        }



        $single = isset($request->type) ? ($request->type == '1' ? 1 : 0) : 0;

        if (Session::has('cart')) {
            if ($item->item_type == 'digital' || $item->item_type == 'license') {
                $check = array_key_exists($input['item_id'], Session::get('cart'));

                if ($check) {
                    $data = ['message' => 'Product already added', 'status' => 'alreadyInCart'];
                    return $data;
                } else {
                    if (array_key_exists($input['item_id'] . '-', Session::get('cart'))) {

                        $data = ['message' => 'Product already added', 'status' => 'alreadyInCart'];
                        return $data;
                    }
                }
            }
        }

        $option_id = [];

        if ($single == 1) {
            $attr_name = [];
            $option_name = [];
            $option_price = [];

            if (count($item->attributes) > 0) {
                foreach ($item->attributes as $attr) {
                    if (isset($attr->options[0]->name)) {
                        $attr_name[] = $attr->name;
                        $option_name[] = $attr->options[0]->name;
                        $option_price[] = $attr->options[0]->price;
                        $option_id[] = $attr->options[0]->id;
                    }
                }
            }

            $input['attr_name'] = $attr_name;
            $input['option_price'] = $option_price;
            $input['option_name'] = $option_name;
            $input['option_id'] = $option_id;

           
            if ($request->quantity != 'NaN') {
                $qty = $request->quantity;
                $qty_check = 1;
            } else {
                $qty = 1;
                $qty_check = 0;
            }
        } else {


            if ($input['attribute_ids']) {
                foreach (explode(',', $input['attribute_ids']) as $attrId) {
                    $attr = Attribute::findOrFail($attrId);
                    $attr_name[] = $attr->name;
                }
                $input['attr_name'] = $attr_name;
            }

            if ($input['options_ids']) {
                foreach (explode(',', $input['options_ids']) as $optionId) {
                    $option = AttributeOption::findOrFail($optionId);
                    $option_name[] = $option->name;
                    $option_price[] = $option->price;
                    $option_id[] = $option->id;
                }
                $input['option_name'] = $option_name;
                $input['option_price'] = $option_price;
            }
        }




        if (!$item) {
            abort(404);
        }


        $option_price = array_sum($input['option_price']);
        $attribute['names'] = $input['attr_name'];
        $attribute['option_name'] = $input['option_name'];

        $itemIdStr = (string) $input['item_id'];
        $prefixedCartRow = $itemIdStr . '-';
        $rawItemKey = trim((string) ($input['item_key'] ?? ''));
        if ($rawItemKey === 'undefined' || strtolower($rawItemKey) === 'null') {
            $rawItemKey = '';
        }

        $cartItemKeyFromRequest = false;
        if ($rawItemKey !== '' && $rawItemKey !== '0' && strncmp($rawItemKey, $prefixedCartRow, strlen($prefixedCartRow)) === 0) {
            $cart_item_key = substr($rawItemKey, strlen($prefixedCartRow));
            $cartItemKeyFromRequest = true;
        } elseif ($rawItemKey !== '' && $rawItemKey !== '0') {
            // Legacy callers: `{item_id}-{suffix}` where suffix must not strip at inner hyphens — limit split to 2.
            $dashParts = explode('-', $rawItemKey, 2);
            if (isset($dashParts[1]) && $dashParts[1] !== '' && $dashParts[0] === $itemIdStr) {
                $cart_item_key = $dashParts[1];
                $cartItemKeyFromRequest = true;
            }
        }

        if (! $cartItemKeyFromRequest) {
            $cart_item_key = str_replace(' ', '', implode(',', $attribute['option_name']));
        }

        $engraving = '';
        if (! empty($input['engraving'])) {
            $engraving = trim((string) $input['engraving']);
            if (mb_strlen($engraving) > 380) {
                $engraving = mb_substr($engraving, 0, 380);
            }
        }

        $ringSize = '';
        if (! empty($input['ring_size'])) {
            $ringSize = trim((string) $input['ring_size']);
            if (mb_strlen($ringSize) > 48) {
                $ringSize = mb_substr($ringSize, 0, 48);
            }
        }

        // Cart row identity from PDP / rebuild: variants are appended below. Updating via full `item_key` from cart UI
        // must not append twice (would mismatch session and duplicate rows).
        if (! $cartItemKeyFromRequest) {
            if ($engraving !== '') {
                $cart_item_key .= '|e-' . md5($engraving);
            }
            if ($ringSize !== '') {
                $cart_item_key .= '|rs-' . md5($ringSize);
            }
        }

        $jewelryMeta = $this->buildJewelryLineMeta($item, $input);
        if (! $cartItemKeyFromRequest) {
            if (! empty($jewelryMeta['metal_type'])) {
                $cart_item_key .= '|mt-' . md5((string) $jewelryMeta['metal_type']);
            }
            if (! empty($jewelryMeta['gold_karat'])) {
                $cart_item_key .= '|gk-' . md5((string) $jewelryMeta['gold_karat']);
            }
            if (! empty($jewelryMeta['color_grade'])) {
                $cart_item_key .= '|dc-' . md5((string) $jewelryMeta['color_grade']);
            }
            if (! empty($jewelryMeta['clarity_grade'])) {
                $cart_item_key .= '|dcl-' . md5((string) $jewelryMeta['clarity_grade']);
            }
        }

        $lineMainUnitPrice = $this->resolveCartLineMainPrice($item, $input, $jewelryMeta);

        $attribute['option_price'] = $input['option_price'];
        $cart = Session::get('cart');
        // if cart is empty then this the first product
        if (!$cart || !isset($cart[$item->id . '-' . $cart_item_key])) {
            $cart[$item->id . '-' . $cart_item_key] = $this->assembleCartLine(
                $option_id,
                $attribute,
                $option_price,
                $item,
                $qty,
                $engraving,
                $ringSize,
                $jewelryMeta,
                $lineMainUnitPrice
            );

            Session::put('cart', $cart);


            $coupon = Session::get('coupon');

            if ($coupon) {
                $promo_code = (object)$coupon['code'];

                $cart = Session::get('cart');
                $cartTotal = PriceHelper::cartTotal($cart, 2);
                $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

                $coupon = [
                    'discount' => $discount['sub'],
                    'code'  => $promo_code
                ];
                Session::put('coupon', $coupon);
            }

            $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
            return $mgs;
        }

        /** New cart line — different options / engraving (previous code silently ignored this case) */
        if (is_array($cart) && ! isset($cart[$item->id . '-' . $cart_item_key])) {
            $cart[$item->id . '-' . $cart_item_key] = $this->assembleCartLine(
                $option_id,
                $attribute,
                $option_price,
                $item,
                $qty,
                $engraving,
                $ringSize,
                $jewelryMeta,
                $lineMainUnitPrice
            );

            Session::put('cart', $cart);

            $coupon = Session::get('coupon');

            if ($coupon) {
                $promo_code = (object) $coupon['code'];
                $cart = Session::get('cart');
                $cartTotal = PriceHelper::cartTotal($cart, 2);
                $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

                $coupon = [
                    'discount' => $discount['sub'],
                    'code' => $promo_code,
                ];
                Session::put('coupon', $coupon);
            }

            return ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
        }


        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$item->id . '-' . $cart_item_key])) {

            $cart = Session::get('cart');

            if ($qty_check == 1) {
                if($isIncrement == 'plus'){
                    $cart[$item->id . '-' . $cart_item_key]['qty'] +=  1;
                }
                else if($isIncrement == 'minus'){
                    $nQty = $cart[$item->id . '-' . $cart_item_key]['qty'];
                    if($nQty == 1){
                        $cart[$item->id . '-' . $cart_item_key]['qty'] = 1;
                    }else{
                        $cart[$item->id . '-' . $cart_item_key]['qty'] -=  1;
                    }
                }
                else{
                    $cart[$item->id . '-' . $cart_item_key]['qty'] = $qty;
                }
            } else {
                $cart[$item->id . '-' . $cart_item_key]['qty'] +=  $qty;
            }

            if ($item->item_type == 'normal') {

                if ($item->stock < (int)$cart[$item->id . '-' . $cart_item_key]['qty']) {
                    $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                    return $data;
                }
            }


            Session::put('cart', $cart);

            $coupon = Session::get('coupon');

            if ($coupon) {
                $promo_code = (object)$coupon['code'];

                $cart = Session::get('cart');
                $cartTotal = PriceHelper::cartTotal($cart, 2);
                $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

                $coupon = [
                    'discount' => $discount['sub'],
                    'code'  => $promo_code
                ];
                Session::put('coupon', $coupon);
            }



            if ($qty_check == 1) {

                if ($isIncrement == 'plus') {

                    $mgs = [
                        'message' => __('Product added successfully'),
                        'qty' => count(Session::get('cart'))
                    ];

                } elseif ($isIncrement == 'minus') {

                    $mgs = [
                        'message' => __('Product removed successfully'),
                        'qty' => count(Session::get('cart'))
                    ];

                } else {

                    $mgs = [
                        'message' => __('Cart updated successfully'),
                        'qty' => count(Session::get('cart'))
                    ];
                }

            } else {

                $mgs = [
                    'message' => __('Product added successfully'),
                    'qty' => count(Session::get('cart'))
                ];
            }

            $qty_check = 0;
            return $mgs;
        }

        $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
        return $mgs;
    }

    public function promoStore($request)
    {

        $input = $request->all();
        $promo_code = PromoCode::where('status', 1)->whereCodeName($input['code'])->where('no_of_times', '>', 0)->first();

        if ($promo_code) {
            $cart = Session::get('cart');
            $cartTotal = PriceHelper::cartTotal($cart, 2);
            $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

            $coupon = [
                'discount' => $discount['sub'],
                'code'  => $promo_code
            ];
            Session::put('coupon', $coupon);

            return [
                'status'  => true,
                'message' => __('Promo code found!')
            ];
        } else {
            return [
                'status'  => false,
                'message' => __('No coupon code found')
            ];
        }
    }



    public function getCart()
    {
        $cart = Session::has('cart') ? Session::get('cart') : null;
        return $cart;
    }

    public function getDiscount($discount, $type, $price)
    {
        if ($type == 'amount') {
            $sub = $discount;
            $total = $price - $sub;
        } else {
            $val = $price / 100;
            $sub = $val * $discount;
            $total = $price - $sub;
        }

        return [
            'sub' => $sub,
            'total' => $total
        ];
    }

    /**
     * Build a cart line without null / empty optional fields (jewelry selections, engraving, ring size).
     *
     * @param  array{metal_type: ?string, gold_karat: ?string, color_grade: ?string, clarity_grade: ?string}  $jewelryMeta
     */
    /**
     * Unit main price in the same base units as items.discount_price (PDP #pdp_line_base_price / items_prices tiers).
     */
    protected function resolveCartLineMainPrice(Item $item, array $input, array $jewelryMeta): float
    {
        $fallback = (float) $item->discount_price;

        if (JewelryDynamicPriceService::itemUsesTierPricing($item)) {
            $tierUnit = JewelryDynamicPriceService::resolveUnitBasePriceForSelection(
                $item,
                $jewelryMeta['gold_karat'] ?? null,
                $jewelryMeta['color_grade'] ?? null,
                $jewelryMeta['clarity_grade'] ?? null
            );
            if ($tierUnit !== null && $tierUnit > 0) {
                return $tierUnit;
            }
        }

        $raw = $input['line_base_price'] ?? null;
        if ($raw === null || $raw === '') {
            return $fallback;
        }
        if (is_string($raw)) {
            $raw = str_replace(',', '.', trim($raw));
        }
        if (! is_numeric($raw)) {
            return $fallback;
        }
        $v = round((float) $raw, 2);
        if ($v <= 0 || $v > 99999999.99) {
            return $fallback;
        }

        return $v;
    }

    protected function assembleCartLine(
        array $option_id,
        array $attribute,
        $option_price,
        Item $item,
        $qty,
        string $engraving,
        string $ringSize,
        array $jewelryMeta,
        float $mainUnitPrice
    ): array {
        $license_name = json_decode($item->license_name, true);
        $license_key = json_decode($item->license_key, true);
        $line = [
            'options_id' => $option_id,
            'attribute' => $attribute,
            'attribute_price' => $option_price,
            'name' => $item->name,
            'slug' => $item->slug,
            'qty' => $qty,
            'price' => PriceHelper::grandPrice($item),
            'main_price' => $mainUnitPrice,
            'photo' => $this->resolveCartLinePhoto($item, $jewelryMeta['metal_type'] ?? null),
            'type' => $item->item_type,
            'item_type' => $item->item_type,
            'item_l_n' => $item->item_type == 'license' && is_array($license_name) ? end($license_name) : null,
            'item_l_k' => $item->item_type == 'license' && is_array($license_key) ? end($license_key) : null,
        ];

        if ($engraving !== '') {
            $line['engraving'] = $engraving;
        }
        if ($ringSize !== '') {
            $line['ring_size'] = $ringSize;
        }

        foreach (['metal_type', 'gold_karat', 'color_grade', 'clarity_grade'] as $jk) {
            if (! empty($jewelryMeta[$jk])) {
                $line[$jk] = $jewelryMeta[$jk];
            }
        }

        return $line;
    }

    /**
     * Cart/checkout thumbnail: use PDP metal variant image when it matches the selected metal_type.
     */
    protected function resolveCartLinePhoto(Item $item, ?string $selectedMetal): string
    {
        $fallback = ImageHelper::storageImageUrl($item->photo ?? null, '');
        if ($selectedMetal === null || trim((string) $selectedMetal) === '') {
            return $fallback !== '' ? $fallback : (string) ($item->photo ?? '');
        }

        $variants = $item->pdp_metal_variants;
        if (! is_array($variants) || $variants === []) {
            return $fallback;
        }

        $needle = trim((string) $selectedMetal);

        foreach ($variants as $v) {
            if (! is_array($v)) {
                continue;
            }

            $matched = false;
            foreach ([$v['key'] ?? '', $v['slug'] ?? '', $v['label'] ?? '', $v['name'] ?? ''] as $c) {
                $c = trim((string) $c);
                if ($c !== '' && strcasecmp($needle, $c) === 0) {
                    $matched = true;
                    break;
                }
            }
            if (! $matched) {
                continue;
            }

            $raw = $v['image'] ?? $v['photo'] ?? '';
            if ($raw === '' || $raw === null) {
                $gallery = $v['images'] ?? $v['gallery'] ?? null;
                if (is_array($gallery)) {
                    foreach ($gallery as $gi) {
                        $gi = trim((string) $gi);
                        if ($gi !== '') {
                            $raw = $gi;
                            break;
                        }
                    }
                }
            }

            if ($raw === '' || $raw === null) {
                continue;
            }

            $resolved = ImageHelper::storageImageUrl((string) $raw, '');
            if ($resolved !== '') {
                return $resolved;
            }
        }

        return $fallback !== '' ? $fallback : (string) ($item->photo ?? '');
    }

    /**
     * Remove legacy PDP keys and empty optional jewelry / notes before persisting orders.cart JSON.
     *
     * @param  array<string, array<string, mixed>>  $cart
     * @return array<string, array<string, mixed>>
     */
    public static function sanitizeCartForOrderJson(array $cart): array
    {
        $legacyKeys = ['pdp_metal_type', 'pdp_gold_karat', 'pdp_diamond_color', 'pdp_diamond_clarity'];
        $optionalKeys = ['metal_type', 'gold_karat', 'color_grade', 'clarity_grade', 'engraving', 'ring_size'];

        $out = [];
        foreach ($cart as $key => $row) {
            if (! is_array($row)) {
                $out[$key] = $row;

                continue;
            }

            $clean = $row;
            foreach ($legacyKeys as $lk) {
                unset($clean[$lk]);
            }
            foreach ($optionalKeys as $ok) {
                if (! array_key_exists($ok, $clean)) {
                    continue;
                }
                $v = $clean[$ok];
                if ($v === null || $v === '') {
                    unset($clean[$ok]);
                }
            }
            $out[$key] = $clean;
        }

        return $out;
    }

    /**
     * Normalize stored item / diamond_attributes option lists for validation.
     *
     * @param  mixed  $stored
     */
    protected function jewelryStoredOptions($stored): array
    {
        $normalized = Item::normalizeJewelryOptionList($stored);

        return $normalized ?? [];
    }

    protected function truncateJewelryString(string $value, int $max): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if ($max > 0 && mb_strlen($value) > $max) {
            return mb_substr($value, 0, $max);
        }

        return $value;
    }

    /**
     * If $catalogOptions is empty, accepted values are PDP-only labels (truncated).
     * Otherwise selection must match a catalog option (case-insensitive).
     */
    protected function jewelryMatchAgainstCatalog(string $candidate, array $catalogOptions, int $maxLen): ?string
    {
        $candidate = $this->truncateJewelryString($candidate, $maxLen);
        if ($candidate === '') {
            return null;
        }
        if ($catalogOptions === []) {
            return $candidate;
        }
        foreach ($catalogOptions as $opt) {
            $opt = trim((string) $opt);
            if ($opt !== '' && strcasecmp($candidate, $opt) === 0) {
                return $opt;
            }
        }

        return null;
    }

    protected function jewelryFirstMatchingCandidate(array $candidates, array $catalogOptions, int $maxLen): ?string
    {
        foreach ($candidates as $c) {
            $m = $this->jewelryMatchAgainstCatalog((string) $c, $catalogOptions, $maxLen);
            if ($m !== null) {
                return $m;
            }
        }

        return null;
    }

    /**
     * Resolve PDP selections using items.metal_type, items.gold_karat and diamond_attributes.color_grade / clarity_grade.
     * Accepts current query keys or legacy pdp_* parameters.
     *
     * @return array{metal_type: ?string, gold_karat: ?string, color_grade: ?string, clarity_grade: ?string}
     */
    protected function buildJewelryLineMeta(Item $item, array $input): array
    {
        $allowedMetal = $this->jewelryStoredOptions($item->metal_type);
        $allowedKarat = $this->jewelryStoredOptions($item->gold_karat);

        $item->loadMissing('diamondAttribute');
        $da = $item->diamondAttribute;

        $allowedColor = [];
        $allowedClarity = [];
        if ($da) {
            $allowedColor = $this->jewelryStoredOptions($da->color_grade);
            $allowedClarity = $this->jewelryStoredOptions($da->clarity_grade);
        }

        $metal = $this->jewelryFirstMatchingCandidate(
            [$input['metal_type'] ?? '', $input['pdp_metal_type'] ?? ''],
            $allowedMetal,
            48
        );
        $karat = $this->jewelryFirstMatchingCandidate(
            [$input['gold_karat'] ?? '', $input['pdp_gold_karat'] ?? ''],
            $allowedKarat,
            48
        );
        $color = $this->jewelryFirstMatchingCandidate(
            [$input['color_grade'] ?? '', $input['pdp_diamond_color'] ?? '', $input['diamond_color'] ?? ''],
            $allowedColor,
            96
        );
        $clarity = $this->jewelryFirstMatchingCandidate(
            [$input['clarity_grade'] ?? '', $input['pdp_diamond_clarity'] ?? '', $input['diamond_quality'] ?? ''],
            $allowedClarity,
            96
        );

        // If the PDP did not send params (or hidden fields are empty), match storefront defaults:
        // first option from items.metal_type / gold_karat and diamond_attributes.*
        if ($metal === null && $allowedMetal !== []) {
            $metal = $allowedMetal[0];
        }
        if ($karat === null && $allowedKarat !== []) {
            $karat = $allowedKarat[0];
        }
        if ($color === null && $allowedColor !== []) {
            $color = $allowedColor[0];
        }
        if ($clarity === null && $allowedClarity !== []) {
            $clarity = $allowedClarity[0];
        }

        return [
            'metal_type' => $metal,
            'gold_karat' => $karat,
            'color_grade' => $color,
            'clarity_grade' => $clarity,
        ];
    }
}
