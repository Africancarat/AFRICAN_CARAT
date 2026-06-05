<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CompareController extends Controller
{

    public function __construct()
    {
        $this->middleware('localize');
    }
    
    public function compare($id)
    {
        return response()->json([
            'status' => 0,
            'message' => __('Side-by-side viewing is for diamonds — use Compare on Diamond Search (up to four stones).'),
            'compare_count' => count(Session::get('diamond_compare', [])),
        ]);
    }


    public function compare_product()
    {
        Session::forget('compare');

        return redirect()->route('diamonds.compare.index')
            ->with('info', __('Fine jewelry comparison is curated in our diamond viewer—add diamonds from Diamond Search.'));
    }



    public function compareRemove($itemId)
    {
        $ids = Session::get('compare', []);
        $newIds = [];
        foreach ($ids as $id) {
            if($itemId != $id){
                $newIds[] = $id;
            }
        }


        if(!count($newIds) == 0){
            Session::put('compare',$newIds);
            return true;
        }else{
            Session::forget('compare');
            return true;
        }


    }
}
