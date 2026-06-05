<?php

namespace App\Http\Controllers\Front;

use App\Helpers\PriceHelper;
use App\Http\Controllers\Controller;
use App\Models\DiamondAttribute;
use App\Models\Item;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;

class DiamondSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('localize');
    }

    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
    {
        $setting = Setting::first();
        $perPage = (int) (($setting ? $setting->view_product : 24) ?: 24);
        $perPage = max(12, min(48, $perPage));

        $q = Item::query()
            ->with(['diamondAttribute'])
            ->where('items.status', 1)
            ->whereHas('diamondAttribute');

        if ($request->filled('lab_grown')) {
            $flag = filter_var($request->input('lab_grown'), FILTER_VALIDATE_INT);
            if ($flag === 0 || $flag === 1) {
                $q->whereHas('diamondAttribute', fn ($j) => $j->where('is_lab_grown', $flag === 1));
            }
        }

        if ($request->filled('carat_min')) {
            $min = (float) $request->input('carat_min');
            $q->whereHas('diamondAttribute', fn ($j) => $j->where('carat_weight', '>=', $min));
        }

        if ($request->filled('carat_max')) {
            $max = (float) $request->input('carat_max');
            $q->whereHas('diamondAttribute', fn ($j) => $j->where('carat_weight', '<=', $max));
        }

        $facetMap = [
            'cut_grade' => 'cut',
            'color_grade' => 'color',
            'clarity_grade' => 'clarity',
            'shape' => 'shape',
        ];

        foreach ($facetMap as $column => $param) {
            $vals = $request->input($param);
            if ($vals === null || $vals === '' || $vals === []) {
                continue;
            }
            $vals = is_array($vals) ? $vals : preg_split('/\s*,\s*/', (string) $vals, -1, PREG_SPLIT_NO_EMPTY);
            $vals = array_values(array_filter($vals, fn ($v) => $v !== '' && $v !== null));
            if ($vals === []) {
                continue;
            }
            $q->whereHas('diamondAttribute', fn ($j) => $j->whereIn($column, $vals));
        }

        if ($request->filled('lab')) {
            $labs = (array) $request->input('lab');
            $labs = array_values(array_filter($labs));
            if ($labs !== []) {
                $q->whereHas('diamondAttribute', fn ($j) => $j->whereIn('lab', $labs));
            }
        }

        foreach ([['table_min', '>='], ['table_max', '<=']] as [$key, $op]) {
            if (! $request->filled($key)) {
                continue;
            }
            $v = (float) $request->input($key);
            $q->whereHas('diamondAttribute', fn ($j) => $j->where('table_pct', $op, $v));
        }

        foreach ([['depth_min', '>='], ['depth_max', '<=']] as [$key, $op]) {
            if (! $request->filled($key)) {
                continue;
            }
            $v = (float) $request->input($key);
            $q->whereHas('diamondAttribute', fn ($j) => $j->where('depth_pct', $op, $v));
        }

        foreach (['fluorescence' => 'fluorescence', 'polish' => 'polish', 'symmetry' => 'symmetry'] as $param => $col) {
            $vals = (array) $request->input($param, []);
            $vals = array_values(array_filter($vals));
            if ($vals !== []) {
                $q->whereHas('diamondAttribute', fn ($j) => $j->whereIn($col, $vals));
            }
        }

        if ($request->filled('price_min')) {
            $v = PriceHelper::convertPrice((float) $request->input('price_min'));
            $q->where('discount_price', '>=', $v);
        }

        if ($request->filled('price_max')) {
            $v = PriceHelper::convertPrice((float) $request->input('price_max'));
            $q->where('discount_price', '<=', $v);
        }

        $sort = $request->input('sort', 'price_asc');
        switch ($sort) {
            case 'price_desc':
                $q->orderByDesc('discount_price');
                break;
            case 'carat_desc':
                $q->orderBy(
                    DiamondAttribute::select('carat_weight')->whereColumn('diamond_attributes.item_id', 'items.id')->limit(1),
                    'desc'
                );
                break;
            case 'carat_asc':
                $q->orderBy(
                    DiamondAttribute::select('carat_weight')->whereColumn('diamond_attributes.item_id', 'items.id')->limit(1),
                    'asc'
                );
                break;
            default:
                $q->orderBy('discount_price', 'asc');
                break;
        }

        /** @var AbstractPaginator $diamonds */
        $diamonds = $q->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('front.diamonds.partials.grid', compact('diamonds'))->render(),
                'pagination' => view('front.diamonds.partials.pagination', compact('diamonds'))->render(),
                'swipe_deck' => $this->swipeDeckFromPaginator($diamonds),
            ]);
        }

        $swipeDeck = $this->swipeDeckFromPaginator($diamonds);

        return view('front.diamonds.index', compact('diamonds', 'swipeDeck'));
    }

    private function swipeDeckFromPaginator(AbstractPaginator $paginator): array
    {
        return collect($paginator->items())->map(function ($d): array {
            /** @var Item $d */
            $da = $d->diamondAttribute;
            $img = $d->thumbnail
                ? \App\Helpers\ImageHelper::storageImageUrl($d->thumbnail)
                : \App\Helpers\ImageHelper::storageImageUrl($d->photo);

            return [
                'id' => $d->id,
                'slug' => $d->slug,
                'name' => $d->name,
                'href' => route('front.product', $d->slug),
                'price' => PriceHelper::grandCurrencyPrice($d),
                'img' => $img,
                'line' => $da ? ($da->shape.' · '.$da->carat_weight.' ct') : '',
            ];
        })->values()->all();
    }
}
