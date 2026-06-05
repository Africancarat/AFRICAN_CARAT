<?php

namespace App\Repositories\Front;

use App\{
    Models\Post,
    Models\Page,
    Models\Order,
};
use App\Helpers\PriceHelper;
use App\Models\Bcategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FrontRepository
{

    public function displayPosts($request){
        if($request->has('category')){
            return Post::with('category')->whereCategoryId(Bcategory::where('slug',$request->category)->first()->id)->latest('id')->paginate(6);
        }
        else if($request->has('search')){
            return Post::with('category')->where('title', 'like', '%' . $request->search . '%')->orWhere('details', 'like', '%' . $request->search  . '%')->latest('id')->paginate(6);
        }

        else if($request->has('tag')){
            return Post::with('category')->where('tags', 'like', '%' . $request->tag . '%')->latest('id')->paginate(6);
        }
        else{
            return Post::with('category')->latest('id')->paginate(6);
        }
    }

    public function displayPost($slug){
        $tagz = '';
        $tags = null;
        $name = Post::pluck('tags')->toArray();
        foreach($name as $nm)
        {
            $tagz .= $nm.',';
        }
        $tags = array_unique(explode(',',$tagz));
        return [
            'posts'       => Post::orderby('id','desc')->take(4)->get(),
            'post'       => Post::whereSlug($slug)->first(),
            'categories' => Bcategory::withCount('posts')->whereStatus(1)->get(),
            'tags'       => array_filter($tags)
        ];
    }

    public function displayPage($slug){
        return Page::whereSlug($slug)->firstOrFail();
    }

    public function reviewSubmit($request)
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)->get();
        $isProductPurchased = false;

        foreach ($orders as $order) {
            $cart = json_decode($order->cart, true);
            if (! is_array($cart)) {
                continue;
            }
            foreach ($cart as $key => $product) {
                if ((int) $request->item_id === (int) PriceHelper::GetItemId($key)) {
                    $isProductPurchased = true;
                    break 2;
                }
            }
        }

        if (! $isProductPurchased) {
            return [
                'errors' => [
                    0 => __('Buy This Product First'),
                ],
            ];
        }

        $jewelryExtras = [];
        if (Schema::hasColumn('reviews', 'occasion')) {
            $jewelryExtras['occasion'] = filled($request->input('occasion'))
                ? Str::limit(trim((string) $request->input('occasion')), 60, '')
                : null;
        }
        if (Schema::hasColumn('reviews', 'ring_size_ordered')) {
            $jewelryExtras['ring_size_ordered'] = filled($request->input('ring_size_ordered'))
                ? Str::limit(trim((string) $request->input('ring_size_ordered')), 48, '')
                : null;
        }
        if (Schema::hasColumn('reviews', 'metal_type_ordered')) {
            $jewelryExtras['metal_type_ordered'] = filled($request->input('metal_type_ordered'))
                ? Str::limit(trim((string) $request->input('metal_type_ordered')), 80, '')
                : null;
        }

        $photoPath = null;
        if (Schema::hasColumn('reviews', 'review_photo') && $request->hasFile('review_photo')) {
            $photoPath = $request->file('review_photo')->store('review-photos', 'public');
            if ($photoPath === false || $photoPath === null) {
                $photoPath = null;
            }
        }

        $existingReview = $user->reviews()->where('item_id', $request->item_id)->first();

        $basePayload = [
            'subject' => $request->subject,
            'rating' => $request->rating,
            'review' => $request->review,
            'status' => 1,
        ];

        $basePayload += $jewelryExtras;

        if (Schema::hasColumn('reviews', 'is_verified_purchase')) {
            $basePayload['is_verified_purchase'] = true;
        }

        if ($photoPath !== null && Schema::hasColumn('reviews', 'review_photo')) {
            $basePayload['review_photo'] = $photoPath;
        }

        if ($existingReview) {
            $update = $basePayload;
            // Do not discard previous photo unless a new upload was provided
            if ($photoPath === null && isset($existingReview->review_photo) && Schema::hasColumn('reviews', 'review_photo')) {
                unset($update['review_photo']);
            }
            $existingReview->update($update);

            return __('Your Review Updated Successfully.');
        }

        $user->reviews()->create(array_merge($basePayload, [
            'item_id' => $request->item_id,
        ]));

        return __('Your Review Submitted Successfully.');
    }
    


}
