<?php

namespace App\Http\Controllers\Front;

use Illuminate\{
    Http\Request,
};

use App\{
    Models\Item,
    Models\Category,
    Http\Controllers\Controller,
};
use App\Helpers\PriceHelper;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Brand;
use App\Models\ChieldCategory;
use App\Models\Setting;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Session;

class CatalogController extends Controller
{
    public function __construct()
    {
        $this->middleware('localize');
    }

	public function index(Request $request)
	{

        // attribute search
        $attr_item_ids = [];
        if($request->attribute){
            $attrubutes_get = Attribute::where('name',$request->attribute)->get();
            foreach($attrubutes_get as $attr_item_id){
                $attr_item_ids[] = $attr_item_id->item_id;
            }
        }

        $option_attr_ids = [];

        if($request->option){
            $option_get = AttributeOption::whereIn('name',explode(',',$request->option))->get();
            foreach($option_get as $option_attr_id){
                $option_attr_ids[] = $option_attr_id->attribute_id;
            }
        }


        $option_wise_item_ids = [];
        foreach(Attribute::whereIn('id',$option_attr_ids)->get() as $attr_item_id){
            $option_wise_item_ids[] = $attr_item_id->item_id;
        }
        $setting = Setting::first();

        $sorting = $request->has('sorting') ?  ( !empty($request->sorting) ? $request->sorting : null ) : null;
        $new = $request->has('new') ?  ( !empty($request->new) ? 1 : null ) : null;
        $feature = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'feature') ? 1 : null ) : null;
        $top = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'top') ? 1 : null ) : null;
        $best = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'best') ? 1 : null ) : null;
        $new = $request->has('quick_filter') ?  ( !empty($request->quick_filter == 'new') ? 1 : null ) : null;
        $brand = $request->has('brand') ?  ( !empty($request->brand) ? Brand::whereSlug($request->brand)->firstOrFail() : null ) : null;
        $search = $request->has('search') ?  ( !empty($request->search) ? $request->search : null ) : null;

        $category = $request->has('category') ? ( !empty($request->category) ? Category::whereSlug($request->category)->firstOrFail() : null ) : null;
        $subcategory = $request->has('subcategory') ? ( !empty($request->subcategory) ? Subcategory::whereSlug($request->subcategory)->firstOrFail() : null ) : null;
        $childcategory = $request->has('childcategory') ? ( !empty($request->childcategory) ? ChieldCategory::where('slug',$request->childcategory)->first() : null ) : null;
        $minPrice = $request->has('minPrice') ?  ( !empty($request->minPrice) ? PriceHelper::convertPrice($request->minPrice) : null ) : null;
        $maxPrice = $request->has('maxPrice') ?  ( !empty($request->maxPrice) ? PriceHelper::convertPrice($request->maxPrice) : null ) : null;
        $tag = $request->has('tag') ?  ( !empty($request->tag) ? $request->tag : null ) : null;

        // CSV facet filters (items.metal_type). Diamond-specific filters now come from diamond_attributes.
        $csvSelected = function ($raw) {
            return collect(explode(',', (string) $raw))
                ->map(function ($v) { return trim((string) $v); })
                ->filter(function ($v) { return $v !== ''; })
                ->values()
                ->all();
        };
        $selected_metal_types = $csvSelected($request->get('metal_type'));

        $csvTokenWhere = function ($query, $column, array $values) {
            if (empty($values)) {
                return $query;
            }
            // Match tokens in legacy CSV, JSON arrays stored as TEXT, or substring (escaped for LIKE).
            return $query->where(function ($q) use ($column, $values) {
                foreach ($values as $v) {
                    $v = trim((string) $v);
                    if ($v === '') {
                        continue;
                    }
                    $like = '%' . addcslashes($v, '%_\\') . '%';
                    $q->orWhere(function ($sub) use ($column, $v, $like) {
                        $sub->where($column, 'like', $like)
                            ->orWhereRaw(
                                "CONCAT(',', REPLACE(COALESCE($column,''), ', ', ','), ',') LIKE ?",
                                ['%,' . addcslashes($v, '%_\\') . ',%']
                            );
                    });
                }
            });
        };
        $items = Item::with(['category', 'itemPrice'])
        ->when($category, function ($query, $category) {
            return $query->where('category_id', $category->id);
        })
        ->when($subcategory, function ($query, $subcategory) {
            return $query->where('subcategory_id', $subcategory->id);
        })
        ->when($childcategory, function ($query, $childcategory) {
            return $query->where('childcategory_id', $childcategory->id);
        })

        ->when($feature, function ($query) {
            return $query->whereIsType('feature');
        })

        ->when($tag, function ($query, $tag) {
            return $query->where('tags', 'like', '%' . $tag . '%');
        })
      

        ->when($new, function ($query) {
            return $query->orderby('id','desc');
        })
        ->when($top, function ($query) {
            return $query->whereIsType('top');
        })
        ->when($best, function ($query) {
            return $query->whereIsType('best');
        })
        ->when($new, function ($query) {
            return $query->whereIsType('new');
        })

        ->when($brand, function ($query, $brand) {
            return $query->where('brand_id', $brand->id);
        })
        ->when($search, function ($query, $search) {
            return $query->whereStatus(1)->where('name', 'like', '%' . $search . '%')->orwhere('name', 'like', '%' . $search . '%');
        })
        ->when($minPrice, function($query, $minPrice) {
          return $query->where('discount_price', '>=', $minPrice);
        })

        ->when($maxPrice, function($query, $maxPrice) {
          return $query->where('discount_price', '<=', $maxPrice);
        })

        ->when($sorting, function($query, $sorting) {
            if($sorting == 'low_to_high'){
                return $query->orderby('discount_price','asc');
            }else{
                return $query->orderby('discount_price','desc');
            }

        })

        ->when($attr_item_ids, function($query, $attr_item_ids) {
          return $query->whereIn('id',$attr_item_ids);
        })
        ->when($option_wise_item_ids, function($query, $option_wise_item_ids) {
          return $query->whereIn('id',$option_wise_item_ids);
        })

        ->when(! empty($selected_metal_types), function ($query) use ($csvTokenWhere, $selected_metal_types) {
            return $csvTokenWhere($query, 'metal_type', $selected_metal_types);
        })

        ->where('status',1)

        ->orderby('id','desc')->paginate($setting->view_product);

     
        $attrubutes_check =[];
       
        $options = AttributeOption::groupby('name')->select('attribute_id','name','id','keyword')->get();
        
        foreach($options as $option){
            if(!in_array(Attribute::withCount('options')->findOrFail($option->attribute_id)->keyword,$attrubutes_check)){
                $attrubutes_check[] = Attribute::withCount('options')->findOrFail($option->attribute_id)->keyword;
            }
        }

        
        $attrubutes = [];

        foreach($attrubutes_check as $attr_new_get){
            $attrubutes[] = Attribute::whereKeyword($attr_new_get)->first();
        }

        // Facet lists for sidebar (unique tokens from CSV columns)
        $facetFromColumn = function ($column) {
            $vals = Item::where('status', 1)->whereNotNull($column)->pluck($column);
            $out = [];
            foreach ($vals as $row) {
                if ($row === null || $row === '') {
                    continue;
                }
                // If Eloquent casts the column to array (JSON stored in TEXT), handle directly.
                if (is_array($row)) {
                    foreach ($row as $piece) {
                        $piece = trim((string) $piece);
                        if ($piece === '') {
                            continue;
                        }
                        $out[$piece] = true;
                    }
                    continue;
                }
                $decoded = json_decode((string) $row, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    foreach ($decoded as $piece) {
                        $piece = trim((string) $piece);
                        if ($piece === '') {
                            continue;
                        }
                        $out[$piece] = true;
                    }
                    continue;
                }
                foreach (explode(',', (string) $row) as $piece) {
                    $piece = trim((string) $piece);
                    if ($piece === '') {
                        continue;
                    }
                    $out[$piece] = true;
                }
            }
            $list = array_keys($out);
            natcasesort($list);
            return array_values($list);
        };
        $facet_metal_types = $facetFromColumn('metal_type');
        $facet_diamond_qualities = [];
        $facet_diamond_colors = [];
      
        $blade = 'front.catalog.index';

        if($request->view_check){
            Session::put('view_catalog',$request->view_check);

        }

        if(Session::has('view_catalog')){
            $checkType = Session::get('view_catalog');
            $name_string_count = 55;
        }else{
            Session::put('view_catalog','grid');
            $checkType = Session::get('view_catalog');
            $name_string_count = 38;
        }


        if($request->ajax()) $blade = 'front.catalog.catalog';

        return view($blade,[
            'attrubutes' => $attrubutes,
            'options' => $options,
            'brand' => $brand,
            'brand' => $brand,
            'brand' => $brand,
            'items' => $items,
            'name_string_count' => $name_string_count,
            'category' => $category,
            'subcategory' => $subcategory,
            'childcategory' => $childcategory,
            'checkType'  => $checkType,
            'brands' => Brand::withCount('items')->whereStatus(1)->get(),
            'categories' => Category::whereStatus(1)->orderby('serial','asc')->withCount(['items' => function($query) {
                $query->where('status',1);
            }])->get(),
            'facet_metal_types' => $facet_metal_types,
            'facet_diamond_qualities' => $facet_diamond_qualities,
            'facet_diamond_colors' => $facet_diamond_colors,
        ]);
	}


    public function viewType($type)
    {
        Session::put('view_catalog',$type);
        return response()->json($type);
    }


    public function suggestSearch(Request $request)
    {
        $category = null;
        if($request->category){
            $category = Category::whereSlug($request->category)->first();
        }
        $search = $request->search;
        $items = Item::with('itemPrice')->whereStatus(1)
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')->orderby('id','desc')->take(10);
        })
        ->when($category, function ($query, $category) {
            return $query->where('category_id', $category->id);
        })
        ->get();

        return view('includes.search_suggest',compact('items'));
    }

}
