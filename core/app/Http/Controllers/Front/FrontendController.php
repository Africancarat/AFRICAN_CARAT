<?php

namespace App\Http\Controllers\Front;

use Illuminate\{
    Http\Request,
    Support\Facades\Session
};

use App\{
    Models\Item,
    Models\Setting,
    Models\Subscriber,
    Helpers\EmailHelper,
    Helpers\FinancingHelper,
    Http\Controllers\Controller,
    Http\Requests\ReviewRequest,
    Http\Requests\SubscribeRequest,
    Repositories\Front\FrontRepository
};
use App\Jobs\EmailSendJob;
use App\Services\JewelryDynamicPriceService;
use App\Services\JewelryPdpMediaService;
use App\Models\Brand;
use App\Models\Menu;
use App\Models\CampaignItem;
use App\Models\Category;
use App\Models\Fcategory;
use App\Models\HomeCutomize;
use App\Models\Order;
use App\Models\PaymentSetting;
use App\Models\Post;
use App\Models\Service;
use App\Models\Slider;
use App\Models\TrackOrder;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class FrontendController extends Controller
{

    /**
     * Constructor Method.
     *
     * @param  \App\Repositories\Front\FrontRepository $repository
     *
     */
    protected $repository;
    public function __construct(FrontRepository $repository)
    {
        $this->repository = $repository;
        $setting = Setting::first();
        if ($setting->recaptcha == 1) {
            Config::set('captcha.sitekey', $setting->google_recaptcha_site_key);
            Config::set('captcha.secret', $setting->google_recaptcha_secret_key);
        }

        $this->middleware('localize');
    }

    // -------------------------------- HOME ----------------------------------------

    public function index()
    {

        $setting = Setting::first();


        $home_customize = HomeCutomize::first();

        // feature category
        $feature_category_ids = json_decode($home_customize->feature_category, true);
        $feature_category_title = $feature_category_ids['feature_title'];
        $feature_category = [];
        for ($i = 1; $i <= 4; $i++) {
            if (!in_array($feature_category_ids['category_id' . $i], $feature_category)) {
                if ($feature_category_ids['category_id' . $i]) {
                    $feature_category[] = $feature_category_ids['category_id' . $i];
                }
            }
        }

        $feature_categories = [];
        foreach ($feature_category as $key => $cat) {
            $feature_categories[] = Category::findOrFail($cat);
        }

        $feature_category_items = [];
        if (count($feature_categories)) {
            $index = '';
            foreach ($feature_categories as $key => $data) {
                if ($data->id == $feature_category_ids['category_id1']) {
                    $index = $key;
                }
            }

            $category = $feature_categories[$index]->id;
            $subcategory = $feature_category_ids['subcategory_id1'];
            $childcategory = $feature_category_ids['childcategory_id1'];

            $feature_category_items = Item::when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
                ->when($subcategory, function ($query, $subcategory) {
                    return $query->where('subcategory_id', $subcategory);
                })
                ->when($childcategory, function ($query, $childcategory) {
                    return $query->where('childcategory_id', $childcategory);
                })
                ->whereStatus(1)->take(10)->orderby('id', 'desc')->get();
        }


        // feature category end
        $home_customize = HomeCutomize::first();
        // popular category

        $popular_category_ids = json_decode($home_customize->popular_category, true);
        $popular_category_title = $popular_category_ids['popular_title'];

        $popular_category = [];
        for ($i = 1; $i <= 4; $i++) {
            if (!in_array($popular_category_ids['category_id' . $i], $popular_category)) {
                if ($popular_category_ids['category_id' . $i]) {
                    $popular_category[] = $popular_category_ids['category_id' . $i];
                }
            }
        }
        $popular_categories = [];
        foreach ($popular_category as $key => $cat) {
            $popular_categories[] = Category::findOrFail($cat);
        }

        $popular_category_items = [];

        if (count($popular_categories) > 0) {
            $index = '';
            foreach ($popular_categories as $key => $data) {
                if ($data->id == $popular_category_ids['category_id1']) {
                    $index = $key;
                }
            }
            $pupular_cateogry_home4 = null;
            if ($setting->theme == 'theme4') {
                $pupular_cateogries_home4 = json_decode($home_customize->home_4_popular_category, true) ?? [];
                $pupular_cateogry_home4 = [];

                if (count($pupular_cateogries_home4) > 0) {
                    foreach ($pupular_cateogries_home4 as $home4category) {
                        $pupular_cateogry_home4[] = Category::with('items')->findOrFail($home4category);
                    }
                }
            }

            // dd($pupular_cateogry_home4);
            $category = $popular_categories[$index]->id;
            $subcategory = $popular_category_ids['subcategory_id1'];
            $childcategory = $popular_category_ids['childcategory_id1'];

            $popular_category_items = Item::when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
                ->when($subcategory, function ($query, $subcategory) {
                    return $query->where('subcategory_id', $subcategory);
                })
                ->when($childcategory, function ($query, $childcategory) {
                    return $query->where('childcategory_id', $childcategory);
                })
                ->whereStatus(1)->get();
        }


        // two column category
        $two_column_category_ids = json_decode($home_customize->two_column_category, true);

        $two_column_category = [];
        for ($i = 1; $i <= 3; $i++) {
            if (isset($two_column_category_ids['category_id' . $i]) && !in_array($two_column_category_ids['category_id' . $i], $two_column_category)) {
                if ($two_column_category_ids['category_id' . $i]) {
                    $two_column_category[] = $two_column_category_ids['category_id' . $i];
                }
            }
        }

        $two_column_categories = Category::whereStatus(1)->whereIn('id', $two_column_category)->orderby('id', 'desc')->get();

        $two_column_category_items1 = [];
        if ($two_column_category_ids['category_id1']) {
            $two_column_category_items1 = Item::where('category_id', $two_column_category_ids['category_id1'])->orderby('id', 'desc')->whereStatus(1)->take(10)->get();
        }
        if ($two_column_category_ids['subcategory_id1']) {
            $two_column_category_items1 = Item::where('subcategory_id', $two_column_category_ids['subcategory_id1'])->whereStatus(1)->where('category_id', $two_column_category_ids['category_id1'])->orderby('id', 'desc')->take(10)->get();
        }
        if ($two_column_category_ids['childcategory_id1']) {
            $two_column_category_items1 = Item::where('childcategory_id', $two_column_category_ids['childcategory_id1'])->whereStatus(1)->where('category_id', $two_column_category_ids['category_id1'])->orderby('id', 'desc')->take(10)->get();
        }

        $two_column_category_items2 = [];
        if ($two_column_category_ids['category_id2']) {
            $two_column_category_items2 = Item::where('category_id', $two_column_category_ids['category_id2'])->orderby('id', 'desc')->whereStatus(1)->take(10)->get();
        }
        if ($two_column_category_ids['subcategory_id2']) {
            $two_column_category_items2 = Item::where('subcategory_id', $two_column_category_ids['subcategory_id2'])->whereStatus(1)->where('category_id', $two_column_category_ids['category_id2'])->orderby('id', 'desc')->take(10)->get();
        }
        if ($two_column_category_ids['childcategory_id2']) {
            $two_column_category_items2 = Item::where('childcategory_id', $two_column_category_ids['childcategory_id2'])->whereStatus(1)->where('category_id', $two_column_category_ids['category_id2'])->orderby('id', 'desc')->take(10)->get();
        }

        $two_column_category_items3 = [];
        if (isset($two_column_category_ids['category_id3'])) {
            if ($two_column_category_ids['category_id3']) {
                $two_column_category_items3 = Item::where('category_id', $two_column_category_ids['category_id3'])->orderby('id', 'desc')->whereStatus(1)->take(10)->get();
            }
            if ($two_column_category_ids['subcategory_id3']) {
                $two_column_category_items3 = Item::where('subcategory_id', $two_column_category_ids['subcategory_id3'])->whereStatus(1)->where('category_id', $two_column_category_ids['category_id3'])->orderby('id', 'desc')->take(10)->get();
            }
            if ($two_column_category_ids['childcategory_id3']) {
                $two_column_category_items3 = Item::where('childcategory_id', $two_column_category_ids['childcategory_id3'])->whereStatus(1)->where('category_id', $two_column_category_ids['category_id3'])->orderby('id', 'desc')->take(10)->get();
            }
        }




        $two_column_categoriess = [];
        foreach ($two_column_categories as $key => $two_category) {
            if ($key == 0) {
                $two_column_categoriess[$key]['name'] = $two_category;
                $two_column_categoriess[$key]['items'] = $two_column_category_items1;
            } elseif ($key == 1) {
                $two_column_categoriess[$key]['name'] = $two_category;
                $two_column_categoriess[$key]['items'] = $two_column_category_items2;
            } else {
                $two_column_categoriess[$key]['name'] = $two_category;
                $two_column_categoriess[$key]['items'] = $two_column_category_items3;
            }
        }


        if ($setting->theme == 'theme1') {
            $sliders = Slider::where('home_page', 'theme1')->get();
        } elseif ($setting->theme == 'theme2') {
            $sliders = Slider::where('home_page', 'theme2')->get();
        } elseif ($setting->theme == 'theme3') {
            $sliders = Slider::where('home_page', 'theme3')->get();
        } else {
            $sliders = Slider::where('home_page', 'theme4')->get();
        }


        // {"title1":"Watchtt","subtitle1":"50% OFF","url1":"#","title2":"Man","subtitle2":"40% OFF","url2":"#","img1":"1637766462banner-h2-4-1.jpeg","img2":"1637766420banner-h2-4-1.jpeg"}

        $spHome = $home_customize->getAttribute('t3_split_path_banner');
        $split_path_banner = $spHome ? (json_decode((string) $spHome, true) ?: []) : [];

        return view('front.index', [
            'hero_banner'   => $home_customize->hero_banner != '[]' ? json_decode($home_customize->hero_banner, true) : null,
            'banner_first'   => json_decode($home_customize->banner_first, true),
            'split_path_banner' => $split_path_banner,
            'sliders'  => $sliders,
            'campaign_items' => CampaignItem::with('item')->whereStatus(1)->whereIsFeature(1)->orderby('id', 'desc')->get(),
            'services' => Service::orderby('id', 'desc')->get(),
            'posts'    => Post::with('category')->orderby('id', 'desc')->take(8)->get(),
            'brands'   => Brand::whereStatus(1)->get(),
            'banner_secend'  => json_decode($home_customize->banner_secend, true),
            'banner_third'   => json_decode($home_customize->banner_third, true),
            'brands'   => Brand::whereStatus(1)->whereIsPopular(1)->get(),
            'products' => Item::with('category')->whereStatus(1),
            'home_page4_banner' => json_decode($home_customize->home_page4, true),
            'pupular_cateogry_home4' => isset($pupular_cateogry_home4) ? $pupular_cateogry_home4 : [],
            // feature category
            'feature_category_items' => $feature_category_items,
            'feature_categories' => $feature_categories,
            'feature_category_title' => $feature_category_title,

            // feature category
            'popular_category_items' => $popular_category_items,
            'popular_categories' => $popular_categories,
            'popular_category_title' => $popular_category_title,

            // two column category
            'two_column_categoriess' => $two_column_categoriess,

        ]);
    }



    public function review_submit()
    {
        return view('back.overlay.index');
    }

    public function slider_o_update(Request $request)
    {
        $setting = Setting::find(1);
        $setting->overlay = $request->slider_overlay;
        $setting->save();
        return redirect()->back();
    }


    public function product($slug)
    {

        $item = Item::with([
            'category',
            'diamondAttribute',
            'galleries',
            'itemPrice'
        ])->whereStatus(1)
            ->whereSlug($slug)
            ->firstOrFail();
        $video = explode('=', $item->video);

        $pdp_slider_images = collect([\App\Helpers\ImageHelper::storageImageUrl($item->photo)]);
        foreach ($item->galleries as $g) {
            $pdp_slider_images->push(\App\Helpers\ImageHelper::storageImageUrl($g->photo));
        }
        $pdp_slider_images = $pdp_slider_images->unique()->values()->all();

        $pdp_metal_images = JewelryPdpMediaService::metalImageUrlsByKey($item);
        $pdp_default_metal = JewelryPdpMediaService::resolveDefaultMetalKey($item, $pdp_metal_images);
        $pdp_viewer_meta = JewelryPdpMediaService::resolveViewerMeta($item);
        $pdp_show_media_gallery = JewelryPdpMediaService::showMediaGallery($pdp_viewer_meta, $pdp_metal_images);
        $pdp_slider_images = JewelryPdpMediaService::mergeDefaultMetalIntoSlider(
            $pdp_slider_images,
            $pdp_metal_images,
            $pdp_default_metal
        );

        $metalFirst = collect($item->pdp_metal_variants ?? [])->first(function ($v) {
            return is_array($v) && (! empty($v['image']) || ! empty($v['photo']));
        });
        $pdp_primary_still = null;
        if ($metalFirst) {
            $mimg = $metalFirst['image'] ?? $metalFirst['photo'] ?? '';
            if ($mimg) {
                $pdp_primary_still = \App\Helpers\ImageHelper::storageImageUrl((string) $mimg);
            }
        }

        $complete_the_look_items = collect();
        if ($item->complete_the_look_ids) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $item->complete_the_look_ids))));
            if ($ids !== []) {
                $complete_the_look_items = Item::with(['category', 'brand'])
                    ->where('status', 1)
                    ->whereIn('id', $ids)
                    ->get()
                    ->sortBy(function ($ci) use ($ids) {
                        $p = array_search((int) $ci->id, $ids, true);

                        return $p === false ? 999 : $p;
                    })
                    ->values();
            }
        }

        $hasJewelryUpsellSignals = $item->diamondAttribute
            || filled($item->complete_the_look_ids)
            || collect($item->pdp_metal_variants ?? [])->isNotEmpty()
            || (isset($item->category->slug) && str_contains(strtolower((string) $item->category->slug), 'ring'));

        $show_pdp_engraving = $hasJewelryUpsellSignals;

        $show_emi_estimate = ($item->diamondAttribute || (float) $item->discount_price >= 4999);

        $show_drop_hint = $hasJewelryUpsellSignals;

        return view('front.catalog.product', [
            'item'          => $item,
            'pdp_line_unit_base' => JewelryDynamicPriceService::initialPdpUnitBasePrice($item),
            'reviews'       => $item->reviews()->where('status', 1)->paginate(3),
            'galleries'     => $item->galleries,
            'video'         => $item->video ? end($video) : '',
            'sec_name'      => isset($item->specification_name) ? json_decode($item->specification_name, true) : [],
            'sec_details'   => isset($item->specification_description) ? json_decode($item->specification_description, true) : [],
            'attributes'    => $item->attributes,
            'related_items' => $item->category->items()->with('itemPrice')->whereStatus(1)->where('id', '!=', $item->id)->take(8)->get(),
            'pdp_slider_images'       => $pdp_slider_images,
            'pdp_primary_still'       => $pdp_primary_still,
            'pdp_metal_images'        => $pdp_metal_images,
            'pdp_default_metal'       => $pdp_default_metal,
            'pdp_viewer_meta'         => $pdp_viewer_meta,
            'pdp_show_media_gallery'  => $pdp_show_media_gallery,
            'complete_the_look_items'   => $complete_the_look_items,
            'show_pdp_engraving'      => $show_pdp_engraving,
            'show_emi_estimate'       => $show_emi_estimate,
            'show_drop_hint'          => $show_drop_hint,
            'financing_pdp'           => FinancingHelper::plansForPrincipalInr((float) $item->discount_price),
        ]);
    }



    public function brands()
    {
        if (Setting::first()->is_brands == 0) {
            return back();
        }
        return view('front.brand', [
            'brands' => Brand::whereStatus(1)->get()
        ]);
    }


    public function blog(Request $request)
    {

        $tagz = '';
        $tags = null;
        $name = Post::pluck('tags')->toArray();
        foreach ($name as $nm) {
            $tagz .= $nm . ',';
        }
        $tags = array_unique(explode(',', $tagz));

        if (Setting::first()->is_blog == 0) return back();

        if ($request->ajax()) return view('front.blog.list', ['posts' => $this->repository->displayPosts($request)]);

        return view('front.blog.index', [
            'posts' => $this->repository->displayPosts($request),
            'recent_posts'       => Post::orderby('id', 'desc')->take(4)->get(),
            'categories' => \App\Models\Bcategory::withCount('posts')->whereStatus(1)->get(),
            'tags'       => array_filter($tags)
        ]);
    }

    public function blogDetails($id)
    {
        $items = $this->repository->displayPost($id);

        return view('front.blog.show', [
            'post' => $items['post'],
            'categories' => $items['categories'],
            'tags' => $items['tags'],
            'posts' => $items['posts'],

        ]);
    }


    // -------------------------------- FAQ ----------------------------------------

    public function faq()
    {
        if (Setting::first()->is_faq == 0) {
            return back();
        }
        $fcategories =  Fcategory::whereStatus(1)->withCount('faqs')->latest('id')->get();
        return view('front.faq.index', ['fcategories' => $fcategories]);
    }

    public function show($slug)
    {
        if (Setting::first()->is_faq == 0) {
            return back();
        }
        $category =  Fcategory::whereSlug($slug)->first();
        return view('front.faq.show', ['category' => $category]);
    }

    // -------------------------------- FAQ ----------------------------------------

    // -------------------------------- CAMPAIGN ----------------------------------------

    public function compaignProduct()
    {
        if (Setting::first()->is_campaign == 0) {
            return back();
        }
        $compaign_items =  CampaignItem::whereStatus(1)->orderby('id', 'desc')->get();
        return view('front.campaign', ['campaign_items' => $compaign_items]);
    }

    // -------------------------------- CAMPAIGN ----------------------------------------


    // -------------------------------- CURRENCY ----------------------------------------
    public function currency($id)
    {
        Session::put('currency', $id);
        return back();
    }
    // -------------------------------- CURRENCY ----------------------------------------


    // -------------------------------- LANGUAGE ----------------------------------------
    public function language($id)
    {
        Session::put('language', $id);
        return back();
    }
    // -------------------------------- LANGUAGE ----------------------------------------


    // -------------------------------- FAQ ----------------------------------------

    public function page($slug)
    {
        return view('front.page', [
            'page' => $this->repository->displayPage($slug)
        ]);
    }

    // -------------------------------- CONTACT ----------------------------------------

    public function contact()
    {
        if (Setting::first()->is_contact == 0) {
            return back();
        }
        return view('front.contact');
    }

    public function contactEmail(Request $request)
    {
        $setting = Setting::first();

        $request->validate([
            'g-recaptcha-response' => $setting->recaptcha == 1 ? 'required|captcha' : '',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'phone' => 'required|max:50',
            'message' => 'required|max:250',
            'honeypot'   => 'max:0',
        ]);

        $input = $request->all();




        $name  = $input['first_name'] . ' ' . $input['last_name'];
        $subject = "Email From " . $name;
        $to = $setting->contact_email;
        $phone = $request->phone;
        $from = $request->email;
        $msg = "Name: " . $name . "<br/>Email: " . $from . "<br/>Phone: " . $phone . "<br/>Message: " . $request->message;

        $emailData = [
            'to' => $to,
            'subject' => $subject,
            'body' => $msg,
        ];



        $setting = Setting::first();
        if ($setting->is_queue_enabled == 1) {
            dispatch(new EmailSendJob($emailData));
        } else {
            $email = new EmailHelper();
            $email->sendCustomMail($emailData);
        }


        Session::flash('success', __('Thank you for contacting with us, we will get back to you shortly.'));
        return redirect()->back();
    }

    // -------------------------------- REVIEW ----------------------------------------

    public function reviews()
    {
        return view('front.reviews');
    }

    public function topReviews()
    {
        return view('front.top-reviews');
    }

    public function reviewSubmit(ReviewRequest $request)
    {
        return response()->json($this->repository->reviewSubmit($request));
    }



    // -------------------------------- SUBSCRIBE ----------------------------------------

    public function subscribeSubmit(SubscribeRequest $request)
    {
        Subscriber::create($request->all());
        return response()->json(__('You Have Subscribed Successfully.'));
    }


    // ---------------------------- TRACK ORDER ----------------------------------------//
    public function trackOrder()
    {
        return view('front.track_order');
    }

    public function track(Request $request)
    {
        $order = Order::where('transaction_number', $request->order_number)->first();

        if ($order) {
            return view('user.order.track', [
                'numbers' => 3,
                'track_orders' => TrackOrder::whereOrderId($order->id)->get()->toArray()
            ]);
        } else {
            return view('user.order.track', [
                'numbers' => 3,
                'error' => 1,
            ]);
        }
    }


    public function maintainance()
    {
        $setting = Setting::first();
        if ($setting->is_maintainance == 0) {
            return redirect(route('front.index'));
        }
        return view('front.maintainance');
    }



    public function finalize()
    {

        Artisan::call('migrate', ['--seed' => true]);
        copy(str_replace('core', '', base_path() . "updater/composer.json"), base_path('composer.json'));
        copy(str_replace('core', '', base_path() . "updater/composer.lock"), base_path('composer.lock'));

        $exists = PaymentSetting::where("unique_keyword", "paytabs")->exists();
        if (!$exists) {
            $jsonString = '{"profile_id":"159330","client_secret":"SNJ9BGGL9W-JKLRTKJ6DR-MTMZ2GMTNW","check_sandbox":1}';
            $gateway = new PaymentSetting();
            $gateway->name = "Paytabs";
            $gateway->unique_keyword = "paytabs";
            $gateway->information = $jsonString;
            $gateway->text = "Paytabs is the faster & safer way to send money. Make an online payment via Paytabs.";
            $gateway->status = 0;

            $gateway->save();
        }


        $menu = Menu::where('language_id', 1)->exists();

        if ($menu == false) {
            $menu = new Menu();
            $menu->language_id = 1;
            $menu->menus = '[{"text":"Home","href":"","icon":"empty","target":"_self","title":"","type":"home"},{"text":"Shop","href":"","icon":"empty","target":"_self","title":"","type":"shop"},{"text":"Campaign","href":"","icon":"empty","target":"_self","title":"","type":"campaign"},{"type":"blog","text":"Blog","href":"","target":"_self"},{"type":"pages","text":"Pages","href":"","target":"_self","children":[{"type":"7","text":"About Us","href":"","target":"_self"},{"type":"14","text":"How It Works","href":"","target":"_self"},{"type":"10","text":"Privacy Policy","href":"","target":"_self"},{"type":"11","text":"Terms & Service","href":"","target":"_self"},{"type":"12","text":"Return Policy","href":"","target":"_self"}]},{"text":"Contact","href":"","icon":"empty","target":"_self","title":"","type":"contact"}]';
            $menu->created_at = Carbon::now();
            $menu->save();
        }

        $setting = Setting::first();
        $setting->version = '6.2';
        $setting->save();


        $sourcePath = 'assets/images';
        $destinationPath = storage_path('app/public/images');



        // Ensure the destination exists
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        if (File::exists($sourcePath)) {
            // Move files and folders
            File::moveDirectory($sourcePath, $destinationPath, true);
        }


        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        // storage:link 
        Artisan::call('storage:unlink');
        Artisan::call('storage:link');

        return redirect(route('front.index'));
    }
}
