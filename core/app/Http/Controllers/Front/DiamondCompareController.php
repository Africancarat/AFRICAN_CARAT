<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Dedicated diamond compare tray (up to 4 stones) — does not reuse generic compare session.
 */
class DiamondCompareController extends Controller
{
    public const SESSION_KEY = 'diamond_compare';

    public function __construct()
    {
        $this->middleware('localize');
    }

    public function index(): View
    {
        $ids = Session::get(self::SESSION_KEY, []);

        $items = collect($ids)->isEmpty()
            ? collect()
            : Item::with('diamondAttribute')
                ->where('status', 1)
                ->whereIn('id', $ids)
                ->whereHas('diamondAttribute')
                ->get()
                ->sortBy(function ($item) use ($ids) {
                    $ids = array_map('intval', $ids);
                    $pos = array_search((int) $item->id, $ids, true);

                    return $pos === false ? 999 : $pos;
                });

        return view('front.diamonds.compare', [
            'items' => $items,
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $id = (int) $request->input('item_id');

        $item = Item::whereKey($id)->where('status', 1)->whereHas('diamondAttribute')->first();

        if (! $item) {
            return response()->json(['success' => false, 'message' => __('Diamond not available.')]);
        }

        $ids = array_values(array_unique(Session::get(self::SESSION_KEY, [])));

        if (in_array($id, $ids, true)) {
            return response()->json([
                'success' => true,
                'message' => __('Already in diamond compare.'),
                'count' => count($ids),
            ]);
        }

        if (count($ids) >= 4) {
            return response()->json([
                'success' => false,
                'message' => __('You can compare up to 4 diamonds.'),
                'count' => 4,
            ]);
        }

        $ids[] = $id;

        Session::put(self::SESSION_KEY, $ids);

        return response()->json([
            'success' => true,
            'message' => __('Added to diamond compare.'),
            'count' => count($ids),
        ]);
    }

    public function remove(int $id): RedirectResponse
    {
        $ids = array_values(array_filter(
            Session::get(self::SESSION_KEY, []),
            fn ($i) => (int) $i !== $id
        ));

        if ($ids === []) {
            Session::forget(self::SESSION_KEY);
        } else {
            Session::put(self::SESSION_KEY, $ids);
        }

        return redirect()->route('diamonds.compare.index')->with('success', __('Removed from comparison.'));
    }

    public function clear(): RedirectResponse
    {
        Session::forget(self::SESSION_KEY);

        return redirect()->route('diamonds.compare.index');
    }
}
