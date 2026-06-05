<?php

namespace App\Http\Controllers\Back;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;

class MenuController extends Controller
{

    public function index(Request $request){

        // $lang = Language::where('code', $request->language)->firstOrFail();
        // $data['lang_id'] = $lang->id;

      
        //app()->setLocale($lang->code);

        $pages = Page::get();

       
        $data["pages"] = $pages;

        $menu = Menu::where('language_id', '1')->first();
        $data['prevMenu'] = '';

        if (!empty($menu)) {
            $data['prevMenu'] = $menu->menus;
        }
     

        return view('back.menu.index', $data);
    }

    public function update(Request $request) {
        // return response()->json(json_decode($request->str, true));

    

        $menus = json_decode($request->str, true);


      //  Menu::where('language_id', $request->language_id)->delete();

        $menu = Menu::where('id', 1)->first();
       
        $menu->language_id = '1';
        $menu->menus = json_encode($menus);
        $menu->save();

        return response()->json(['status' => 'success', 'message' => __('Menu updated successfully!')]);
    }
}
