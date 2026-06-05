<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Item $item) {
            foreach (['photo', 'thumbnail'] as $attribute) {
                $value = $item->getAttribute($attribute);
                if (! is_string($value) || trim($value) === '') {
                    continue;
                }
                $fixed = ImageHelper::normalizeStorageImagePath(trim($value));
                if ($fixed !== $value) {
                    $item->setAttribute($attribute, $fixed);
                }
            }
        });
    }

    protected $fillable = ['category_id','subcategory_id','childcategory_id','brand_id','name','slug','sku','tags','video','sort_details','specification_name','specification_description','is_specification','details','photo','thumbnail','discount_price','previous_price','stock','meta_keywords','meta_description','status','is_type','tax_id','date','item_type','file','link','file_type','license_name','license_key','affiliate_link',"seller_id","complete_the_look_ids","pdp_metal_variants","pdp_ar_model_url","metal_type","gold_karat","gold_weight","labour_per_gram","igi_per_carat","margin_type","margin_value"];

    protected $casts = [
        'pdp_metal_variants' => 'array',
        'metal_type' => 'array',
        'gold_karat' => 'array',
    ];

    /**
     * Normalize admin / import input to a flat list for JSON array columns (metal_type, gold_karat).
     * Accepts JSON array strings, comma-separated CSV, or a PHP array (e.g. multi-select).
     */
    public static function normalizeJewelryOptionList($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            $out = [];
            foreach ($value as $v) {
                $s = is_string($v) || is_numeric($v) ? trim((string) $v) : '';
                if ($s !== '') {
                    $out[] = $s;
                }
            }

            return $out === [] ? null : array_values($out);
        }

        $str = trim((string) $value);
        if ($str === '') {
            return null;
        }

        $decoded = json_decode($str, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $out = [];
            foreach ($decoded as $v) {
                $s = trim((string) $v);
                if ($s !== '') {
                    $out[] = $s;
                }
            }

            return $out === [] ? null : array_values($out);
        }

        $out = [];
        foreach (explode(',', $str) as $piece) {
            $piece = trim((string) $piece);
            if ($piece !== '') {
                $out[] = $piece;
            }
        }

        return $out === [] ? null : array_values($out);
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category')->withDefault();
    }

    public function subcategory()
    {
        return $this->belongsTo('App\Models\Subcategory')->withDefault();
    }

    public function childcategory()
    {
        return $this->belongsTo('App\Models\ChieldCategory')->withDefault();
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand')->withDefault();
    }

    public function campaigns()
    {
        return $this->hasMany('App\Models\CampaignItem');
    }

    public function tax()
    {
        return $this->belongsTo('App\Models\Tax')->withDefault();
    }

    public function attributes()
    {
        return $this->hasMany('App\Models\Attribute');
    }

    public function galleries()
    {
        return $this->hasMany('App\Models\Gallery');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function diamondAttribute()
    {
        return $this->hasOne(DiamondAttribute::class);
    }

    public static function taxCalculate($item)
    {
        if($item->tax){
            $price = $item->discount_price;
            $percentage = $item->tax->value;
            $tax = ($price * $percentage) / 100;
            return $tax;
        }else{
            return 0;
        }
        
    }




    public function getWishlistItemId()
    {
        return Wishlist::whereItemId($this->id)->first()->id;
    }
    public function itemPrice()
    {
        return $this->belongsTo(ItemPrice::class, 'item_price_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User','vendor_id')->withDefault();
    }


    public function is_stock()
    {
        $item = $this;

        // License products
        if ($item->item_type == 'license') {

            if (!$item->license_key) {
                return false;
            }

            $licenseKey = json_decode($item->license_key, true);

            return is_array($licenseKey) && count($licenseKey) > 0;
        }

        // Digital products
        if ($item->item_type == 'digital') {
            return true;
        }

        // Affiliate products
        if ($item->item_type == 'affiliate') {
            return true;
        }

        // Physical products (normal + migrated products with blank item_type)
        if ($item->item_type == 'normal' || empty($item->item_type)) {
            return (int) $item->stock > 0;
        }

        return false;
    }}