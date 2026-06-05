<?php

namespace App\Repositories\Back;

use App\{
    Models\Item,
    Models\Gallery,
    Helpers\ImageHelper
};
use App\Models\Currency;

class ItemRepository
{

    /**
     * Store item.
     *
     * @param  \App\Http\Requests\ItemRequest  $request
     * @return void
     */

    public function store($request)
    {
        
        $input = $request->all();
        if ($file = $request->file('photo')) {
            $images_name = ImageHelper::ItemhandleUploadedImage($request->file('photo'),'images');

            $input['photo'] = $images_name[0];
            $input['thumbnail'] = $images_name[1];
        }

        $curr = Currency::where('is_default',1)->first();
        $input['discount_price'] = $request->discount_price / $curr->value;
        $input['previous_price'] = $request->previous_price / $curr->value;

        if($request->has('meta_keywords')){
            $input['meta_keywords'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->meta_keywords);
        }

        if($request->has('is_social')){
            $input['social_icons'] = json_encode($input['social_icons']);
            $input['social_links'] = json_encode($input['social_links']);
        }else{
            $input['is_social']    = 0;
            $input['social_icons'] = null;
            $input['social_links'] = null;
        }

        if($request->has('tags')){
            $input['tags'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->tags);
        }

        if($request->has('is_specification')){
            $input['specification_name'] = json_encode($input['specification_name']);
            $input['specification_description'] = json_encode($input['specification_description']);
        }else{
            $input['is_specification']    = 0;
            $input['specification_name'] = null;
            $input['specification_description'] = null;
        }

        if($request->has('license_name') && $request->has('license_key')){
            $input['license_name'] = json_encode($input['license_name']);
            $input['license_key'] = json_encode($input['license_key']);
        }else{
            $input['license_name'] = null;
            $input['license_key'] = null;
        }

        // digital product file upload
        if($request->item_type == 'digital'){
            if($request->hasFile('file')){
                $file = $request->file;
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/files',$name);
                $input['file'] = $name;
            }
        }

        if($request->item_type == 'license'){
            if($request->hasFile('file')){
                $file = $request->file;
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/files',$name);
                $input['file'] = $name;
            }
        }


        $input['is_type'] = 'undefine';

        $input['metal_type'] = Item::normalizeJewelryOptionList($request->input('metal_type'));
        $input['gold_karat'] = Item::normalizeJewelryOptionList($request->input('gold_karat'));
        $input = array_merge($input, $this->jewelryCostFieldsFromRequest($request));

        $item_id = Item::create($input)->id;

        if(isset($input['galleries'])){
            $this->galleriesUpdate($request,$item_id);
        }

        // Build per-metal PDP gallery mapping:
        // - Yellow Gold = Featured image + Gallery images
        // - Rose/White = uploaded metal-specific images (optional)
        $item = Item::with('galleries')->find($item_id);
        if ($item) {
            $yellow = [];
            if (! empty($item->photo)) {
                $yellow[] = (string) $item->photo;
            }
            foreach ($item->galleries as $g) {
                if (! empty($g->photo)) {
                    $yellow[] = (string) $g->photo;
                }
            }
            $yellow = array_values(array_unique(array_filter($yellow)));

            $variants = [];
            if ($yellow !== []) {
                $variants[] = [
                    'key' => 'YELLOW GOLD',
                    'label' => 'YELLOW GOLD',
                    'image' => $yellow[0],
                    'images' => $yellow,
                ];
            }

            foreach ([
                'ROSE GOLD' => 'pdp_metal_images_rose',
                'WHITE GOLD' => 'pdp_metal_images_white',
            ] as $label => $field) {
                $files = $request->file($field);
                if (! is_array($files) || $files === []) {
                    continue;
                }
                $names = [];
                foreach ($files as $f) {
                    if (! $f) continue;
                    $name = ImageHelper::handleUploadedImage($f, 'images');
                    if ($name) $names[] = $name;
                }
                if ($names !== []) {
                    $variants[] = [
                        'key' => $label,
                        'label' => $label,
                        'image' => $names[0],
                        'images' => $names,
                    ];
                }
            }

            if ($variants !== []) {
                $item->pdp_metal_variants = $variants;
                $item->save();
            }
        }

        return $item_id;

    }

    /**
     * Update item.
     *
     * @param  \App\Http\Requests\ItemRequest  $request
     * @return void
     */

    public function update($item,$request)
    {
        $input = $request->all();

        if ( $request->file('photo')) {

            $images_name = ImageHelper::ItemhandleUpdatedUploadedImage($request->photo,'images',$item,'images','photo');
            $input['photo'] = $images_name[0];
            $input['thumbnail'] = $images_name[1];
        }


        if($request->has('meta_keywords')){
            $input['meta_keywords'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->meta_keywords);
        }

        $curr = Currency::where('is_default',1)->first();
        $input['discount_price'] = $request->discount_price / $curr->value;
        $input['previous_price'] = $request->previous_price / $curr->value;


        if($request->has('is_social')){
            $input['social_icons'] = json_encode($input['social_icons']);
            $input['social_links'] = json_encode($input['social_links']);
        }else{
            $input['is_social']    = 0;
            $input['social_icons'] = null;
            $input['social_links'] = null;
        }

        if($request->has('tags')){
            $input['tags'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->tags);
        }

        if($request->has('is_specification')){
            $input['specification_name'] = json_encode($input['specification_name']);
            $input['specification_description'] = json_encode($input['specification_description']);
        }else{
            $input['is_specification']    = 0;
            $input['specification_name'] = null;
            $input['specification_description'] = null;
        }

        if($request->has('license_name') && $request->has('license_key')){
            $input['license_name'] = json_encode($input['license_name']);
            $input['license_key'] = json_encode($input['license_key']);
        }else{
            $input['license_name'] = null;
            $input['license_key'] = null;
        }


        if($request->item_type == 'digital'){
            if(!$request->hasFile('file')){
                if($request->link){
                    if(file_exists('assets/files/'.$item->file)){
                        unlink('assets/files/'.$item->file);
                    }
                    $input['file'] = null;
                }
            }
        }
        // digital product file upload
        if($request->item_type == 'digital'){
            if($request->hasFile('file')){
                if($item->file){
                    if(file_exists('assets/files/'.$item->file)){
                        unlink('assets/files/'.$item->file);
                    }
                }

                $file = $request->file;
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/files',$name);
                $input['file'] = $name;
                $input['link'] = null;
            }
        }

        $input['metal_type'] = Item::normalizeJewelryOptionList($request->input('metal_type'));
        $input['gold_karat'] = Item::normalizeJewelryOptionList($request->input('gold_karat'));
        $input = array_merge($input, $this->jewelryCostFieldsFromRequest($request));

        // Optional: per-metal PDP image mapping (stored in items.pdp_metal_variants as JSON array).
        // Merge with existing variants unless a metal is re-uploaded (then replace that metal's list).
        $existing = is_array($item->pdp_metal_variants) ? $item->pdp_metal_variants : [];
        $byKey = [];
        foreach ($existing as $row) {
            if (! is_array($row)) continue;
            $k = strtoupper((string) ($row['key'] ?? $row['label'] ?? $row['slug'] ?? ''));
            if ($k === '') continue;
            $byKey[$k] = $row;
        }

        $metalUploads = [
            'ROSE GOLD' => 'pdp_metal_images_rose',
            'WHITE GOLD' => 'pdp_metal_images_white',
        ];
        $touched = false;
        foreach ($metalUploads as $label => $field) {
            $files = $request->file($field);
            if (! is_array($files) || $files === []) {
                continue;
            }
            $names = [];
            foreach ($files as $f) {
                if (! $f) continue;
                $name = ImageHelper::handleUploadedImage($f, 'images');
                if ($name) $names[] = $name;
            }
            if ($names !== []) {
                $byKey[strtoupper($label)] = [
                    'key' => $label,
                    'label' => $label,
                    'image' => $names[0],
                    'images' => $names,
                ];
                $touched = true;
            }
        }

        if ($touched) {
            $input['pdp_metal_variants'] = array_values($byKey);
        }

        $item->update($input);
        if(isset($input['galleries'])){
            $this->galleriesUpdate($request,$item->id);
        }

        // Keep Yellow Gold mapping in sync with Featured image + Gallery images.
        $item->loadMissing('galleries');
        $yellow = [];
        if (! empty($item->photo)) {
            $yellow[] = (string) $item->photo;
        }
        foreach ($item->galleries as $g) {
            if (! empty($g->photo)) {
                $yellow[] = (string) $g->photo;
            }
        }
        $yellow = array_values(array_unique(array_filter($yellow)));
        if ($yellow !== []) {
            $byKey['YELLOW GOLD'] = [
                'key' => 'YELLOW GOLD',
                'label' => 'YELLOW GOLD',
                'image' => $yellow[0],
                'images' => $yellow,
            ];
            $item->pdp_metal_variants = array_values($byKey);
            $item->save();
        }
    }

    public function highlight($item,$request)
    {
        $input = $request->all();
        if($request->is_type != 'flash_deal'){
            $input['date'] = null;
        }
        $item->update($input);
    }

    /**
     * Delete item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($item)
    {
        if($item->galleries()->count() > 0){
            foreach($item->galleries as $gallery){
                $this->galleryDelete($gallery);
            }
        }

        if($item->campaigns->count() > 0){
            $item->campaigns()->delete();
        }
        if($item->reviews->count() > 0){
            $item->reviews()->delete();
        }

        if($item->attributes()->count() > 0){
            foreach($item->attributes as $attribute){
                $attribute->options()->delete();
            }
            $item->attributes()->delete();
        }

        ImageHelper::handleDeletedImage($item,'photo','images');
        ImageHelper::handleDeletedImage($item,'thumbnail','images');
        if($item->item_type == 'digital' && $item->file){
            ImageHelper::handleDeletedImage($item,'file','images');
        }
        $item->delete();
    }

    /**
     * Update gallery.
     *
     * @param  \App\Http\Requests\GalleryRequest  $request
     * @return void
     */

    public function galleriesUpdate($request,$item_id=null)
    {
        Gallery::insert($this->storeImageData($request,$item_id));
    }

    /**
     * Delete gallery.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function galleryDelete($gallery)
    {
        ImageHelper::handleDeletedImage($gallery,'photo','images');
        $gallery->delete();
    }

    /**
     * Custom Function.
     * @return void
     */

    public function storeImageData($request,$item_id=null)
    {
        $storeData = [];
        if ($galleries = $request->file('galleries')) {
            foreach($galleries as $key => $gallery){
                $storeData[$key] = [
                    'photo'=>  ImageHelper::handleUploadedImage($gallery,'images'),
                    'item_id' => $item_id ? $item_id : $request['item_id'],
                ];
            }
        }
        return $storeData;
    }

    /**
     * Normalize jewelry cost / margin fields from admin create/update forms.
     *
     * @return array<string, float|string>
     */
    private function jewelryCostFieldsFromRequest($request): array
    {
        $parse = function (string $key, int $decimals) use ($request): float {
            $raw = $request->input($key);
            if ($raw === null || $raw === '') {
                return 0.0;
            }
            $s = str_replace(',', '.', trim((string) $raw));
            if ($s === '' || ! is_numeric($s)) {
                return 0.0;
            }

            return round((float) $s, $decimals);
        };

        $marginType = strtolower(trim((string) $request->input('margin_type', 'percent')));
        if ($marginType !== 'fixed') {
            $marginType = 'percent';
        }

        return [
            'gold_weight' => $parse('gold_weight', 3),
            'labour_per_gram' => $parse('labour_per_gram', 2),
            'igi_per_carat' => $parse('igi_per_carat', 2),
            'margin_type' => $marginType,
            'margin_value' => $parse('margin_value', 2),
        ];
    }

}
