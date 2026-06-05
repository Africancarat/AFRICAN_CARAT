<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\HomeCutomize;
use Illuminate\Http\Request;

class HomePageController extends Controller
{

     /**
     * Constructor Method.
     *
     * Setting Authentication
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }


    public function index(){
        $data = HomeCutomize::first();

        $spRaw = $data->getAttribute('t3_split_path_banner');
        $split_path_banner = $spRaw ? (json_decode((string) $spRaw, true) ?: []) : [];

        return view('back.home-page.index',[
            'hero_banner' => json_decode($data->hero_banner,true),
            'first_banner' => json_decode($data->banner_first,true),
            'split_path_banner' => $split_path_banner,
            'secend_banner' => json_decode($data->banner_secend,true),
            'third_banner' => json_decode($data->banner_third,true),
            'popular_category' => json_decode($data->popular_category,true),
            'three_column_category' => json_decode($data->two_column_category,true),
            'feature_category' => json_decode($data->feature_category,true),
            'home4_banner' => json_decode($data->home_page4,true),
            'home_4_popular_category' => json_decode($data->home_4_popular_category,true),
        ]);
    }

    public function hero_banner_update(Request $request)
    {
        $request->validate([
            'img1' => 'image',
            'img2' => 'image',
            'title1' => 'required|max:200',
            'title2' => 'required|max:200',
            'subtitle1' => 'required|max:200',
            'url1' => 'required|max:200',
            'url2' => 'required|max:200',

        ]);
        $all_images_names = ['img1','img2'];
        $input = $request->all();
        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->hero_banner,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images',isset($check[$single_image]) ? $check[$single_image] : null);
            }
        }

        unset($input['_token']);
        $data = HomeCutomize::first();
        foreach(json_decode($data->hero_banner,true) as $key => $value){
            if(isset($input[$key])){
                $input[$key] =  $input[$key];
            }else{
                $input[$key] = $value;
            }
        }


        $data->hero_banner = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }
    public function first_banner_update(Request $request)
    {
        $request->validate([
            'img1' => 'nullable|image',
            'img2' => 'nullable|image',
            'img3' => 'nullable|image',
            'img4' => 'nullable|image',
            'img5' => 'nullable|image',
            'firsturl1' => 'required|max:200',
            'firsturl2' => 'required|max:200',
            'firsturl3' => 'required|max:200',
            'firsturl4' => 'required|max:200',
            'firsturl5' => 'required|max:200',
        ]);
        $all_images_names = ['img1', 'img2', 'img3', 'img4', 'img5'];

        $input = $request->all();

        $data = HomeCutomize::first();
        $check = json_decode($data->banner_first, true) ?: [];

        foreach ($all_images_names as $single_image) {
            if ($request->hasFile($single_image)) {
                $input[$single_image] = ImageHelper::handleUploadedImage(
                    $request->file($single_image),
                    'images',
                    $check[$single_image] ?? null
                );
            } else {
                $input[$single_image] = $check[$single_image] ?? '';
            }
        }

        unset($input['_token']);

        $data->banner_first = json_encode($input, true);
        $data->update();

        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }

    /**
     * Theme 3: full-width split promo (background + optional PNG cutout, two CTAs).
     * Stored as JSON in home_cutomizes.t3_split_path_banner; images in storage/images.
     */
    public function split_path_banner_update(Request $request)
    {
        $request->validate([
            'bg_image' => 'nullable|image',
            'fg_image' => 'nullable|image',
            'bg_color' => 'nullable|max:32',
            'kicker' => 'nullable|max:255',
            'headline' => 'nullable|max:255',
            'body' => 'nullable|max:2000',
            'watermark_text' => 'nullable|max:255',
            'btn1_label' => 'nullable|max:200',
            'btn1_url' => 'nullable|max:500',
            'btn2_label' => 'nullable|max:200',
            'btn2_url' => 'nullable|max:500',
            'or_label' => 'nullable|max:32',
            'foot_prefix' => 'nullable|max:255',
            'foot_link_text' => 'nullable|max:255',
            'foot_link_url' => 'nullable|max:500',
        ]);

        $data = HomeCutomize::first();
        $merged = [];
        $raw = $data->getAttribute('t3_split_path_banner');
        if ($raw) {
            $merged = json_decode((string) $raw, true);
            if (! is_array($merged)) {
                $merged = [];
            }
        }

        foreach (['bg_image', 'fg_image'] as $k) {
            if ($request->hasFile($k)) {
                $merged[$k] = ImageHelper::handleUploadedImage(
                    $request->file($k),
                    'images',
                    $merged[$k] ?? null
                );
            } elseif (! array_key_exists($k, $merged)) {
                $merged[$k] = '';
            }
        }

        $textKeys = [
            'bg_color', 'kicker', 'headline', 'body', 'watermark_text',
            'btn1_label', 'btn1_url', 'btn2_label', 'btn2_url', 'or_label',
            'foot_prefix', 'foot_link_text', 'foot_link_url',
        ];
        foreach ($textKeys as $key) {
            if ($request->exists($key)) {
                $merged[$key] = $request->input($key);
            }
        }

        $data->t3_split_path_banner = json_encode($merged, JSON_UNESCAPED_UNICODE);
        $data->update();

        return redirect()->back()->withSuccess(__('Banner Update Successfully'));
    }

    public function secend_banner_update(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image',
            'breadcrumb' => 'nullable|max:255',
            'heading' => 'required|max:255',
            'description' => 'nullable|max:2000',
            'bullet_points' => 'nullable|max:2000',
            'button_label' => 'required|max:200',
            'button_url' => 'required|max:500',
        ]);

        $data = HomeCutomize::first();
        $check = json_decode($data->banner_secend, true) ?: [];
        $currentImage = $check['image'] ?? $check['img1'] ?? null;

        $payload = [
            'image' => $request->hasFile('image')
                ? ImageHelper::handleUploadedImage($request->file('image'), 'images', $currentImage)
                : ($currentImage ?? ''),
            'breadcrumb' => (string) ($request->input('breadcrumb') ?? ''),
            'heading' => (string) ($request->input('heading') ?? ''),
            'description' => (string) ($request->input('description') ?? ''),
            'bullet_points' => (string) ($request->input('bullet_points') ?? ''),
            'button_label' => (string) ($request->input('button_label') ?? ''),
            'button_url' => (string) ($request->input('button_url') ?? ''),
        ];

        $data->banner_secend = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $data->update();
        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }

    public function third_banner_update(Request $request)
    {

        $request->validate([
            'img1' => 'image',
            'img2' => 'image',
            'url1' => 'required|max:200',
            'url2' => 'required|max:200',
        ]);
        $all_images_names = ['img1','img2'];

        $input = $request->all();
        $data = HomeCutomize::first();

        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->banner_third,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images',$check[$single_image]);
            }else{
                $check = json_decode($data->banner_third,true);
                $input[$single_image] = $check[$single_image];
            }
        }
        unset($input['_token']);




        $data->banner_third = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }


    public function popular_category_update(Request $request)
    {
        $request->validate([
            'popular_title' => 'required|max:255',
        ]);
        $input = $request->all();
        unset($input['_token']);
        $data = HomeCutomize::first();
        $data->popular_category = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Popular Category Update Successfully'));
    }

    public function tree_column_category_update(Request $request)
    {
        $input = $request->all();
        unset($input['_token']);
        $data = HomeCutomize::first();
        $data->two_column_category = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Tree Column Category Update Successfully'));
    }


    public function feature_category_update(Request $request)
    {
        $request->validate([
            'feature_title' => 'required|max:255',
        ]);
        $input = $request->all();
        unset($input['_token']);
        $data = HomeCutomize::first();
        $data->feature_category = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Popular Category Update Successfully'));
    }


    public function homepage4update(Request $request)
    {
        $request->validate([
            'img1' => 'image',
            'img2' => 'image',
            'img3' => 'image',
            'img4' => 'image',
            'img5' => 'image',
            'url1' => 'required|max:200',
            'url2' => 'required|max:200',
            'url3' => 'required|max:200',
            'url4' => 'required|max:200',
            'url5' => 'required|max:200',
            'label1' => 'required|max:200',
            'label2' => 'required|max:200',
            'label3' => 'required|max:200',
            'label4' => 'required|max:200',
            'label5' => 'required|max:200',
        ]);
        $all_images_names = ['img1','img2','img3','img4','img5'];
        $input = $request->all();
        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->home_page4,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images',$check[$single_image]);
            }
        }

        unset($input['_token']);

        $data = HomeCutomize::first();
        if(!$data->home_page4){
        $data->home_page4 = json_encode($input,true);
        $data->update();
        }else{
            foreach(json_decode($data->home_page4,true) as $key => $value){
                if(isset($input[$key])){
                    $input[$key] =  $input[$key];
                }else{
                    $input[$key] = $value;
                }
            }
            $data->home_page4 = json_encode($input,true);
            $data->update();
        }

        return redirect()->back()->withSuccess(__('Banner Update Successfully'));


    }


    public function homepage4categoryupdate(Request $request)
    {
       $category = json_encode($request->home_4_popular_category,true);
       $data = HomeCutomize::first();
       $data->home_4_popular_category = $category;
       $data->update();
       return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }
}
