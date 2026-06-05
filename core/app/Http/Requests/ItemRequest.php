<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['metal_type', 'gold_karat', 'color_grade', 'clarity_grade'] as $key) {
            if (! $this->has($key)) {
                continue;
            }
            $raw = $this->input($key);
            if (is_string($raw)) {
                $normalized = Item::normalizeJewelryOptionList($raw);
                $this->merge([$key => $normalized ?? []]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {


        $type_required = $this->item_type == 'digital' || $this->item_type == 'license' ? '' : 'required';



        $check_link = $this->file_type == 'link' ? 'required' : '';
        if($this->item_type == 'digital'){
            if($this->item){
                $check_file = '';
            }else{
                $check_file = $this->item_type == 'digital' && $this->file_type == 'file' ? 'required' : '';
            }
        }elseif($this->item_type == 'license'){
            if($this->item){
                $check_file = '';
            }else{
                $check_file = $this->item_type == 'license' && $this->file_type == 'file' ? 'required' : '';
            }
        }else{
            $check_file = '';
        }
        $id = $this->item ? ',' . $this->item->id : '';
        $required = $this->item ? '' : 'required|';


        return [
            'name'            => 'required|max:255',
            'slug'            => 'required','unique:items,slug' . $id, 'regex:/^[a-zA-Z0-9-]+$/',
            'category_id'     => 'required',
            'details'         => 'required',
            'link'            => $check_link,
            'file'            => $check_file.'|file|mimes:zip',
            'sort_details'    => 'required',
            'discount_price'  => 'required|max:50',
            'previous_price'  => 'max:50',
            'stock'           => 'numeric|max:9999999999',
            'tax_id'          => 'required',
            'photo'           => $required, 'mimes:jpeg,jpg,png,svg',
            'pdp_metal_images_rose' => 'nullable|array',
            'pdp_metal_images_rose.*' => 'nullable|mimes:jpeg,jpg,png,webp',
            'pdp_metal_images_white' => 'nullable|array',
            'pdp_metal_images_white.*' => 'nullable|mimes:jpeg,jpg,png,webp',
            'metal_type'      => 'nullable',
            'metal_type.*'    => 'nullable|string|max:120',
            'gold_karat'      => 'nullable',
            'gold_karat.*'    => 'nullable|string|max:120',
            'has_diamond'     => 'nullable|boolean',
            'carat_weight'    => $this->has_diamond ? 'nullable|numeric|min:0|max:999' : 'nullable|numeric|min:0|max:999',
            'shape'           => $this->has_diamond ? 'nullable|string|max:50' : 'nullable|string|max:50',
            'cut_grade'       => $this->has_diamond ? 'nullable|string|max:50' : 'nullable|string|max:50',
            'color_grade'     => 'nullable',
            'color_grade.*'   => 'nullable|string|max:50',
            'clarity_grade'   => 'nullable',
            'clarity_grade.*' => 'nullable|string|max:50',
            'lab'             => $this->has_diamond ? 'nullable|string|max:50' : 'nullable|string|max:50',
            'certificate_number' => $this->has_diamond ? 'nullable|string|max:120' : 'nullable|string|max:120',
            'video_360_url'   => $this->has_diamond ? 'nullable|string|max:512' : 'nullable|string|max:512',
            'is_lab_grown'    => 'nullable|boolean',
            'gold_weight'     => 'nullable|numeric|min:0',
            'labour_per_gram' => 'nullable|numeric|min:0',
            'igi_per_carat'   => 'nullable|numeric|min:0',
            'margin_type'     => 'nullable|string|in:percent,fixed',
            'margin_value'    => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {

        return [
            'name.required'            =>  __('Name field is required.'),
            'tax_id.required'          =>  __('Tax field is required.'),
            'category_id.required'     =>  __('Category field is required.'),
            'brand_id.required'        =>  __('Brand field is required.'),
            'slug.required'            =>  __('Slug field is required.'),
            'slug.unique'              =>  __('This slug has already been taken.'),
            'details.required'         =>  __('Description field is required.'),
            'sort_details.required'    =>  __('Sort Description field is required.'),
            'discount_price.required'  =>  __('Current Price field is required.'),
            'stock.required'           =>  __('Stock field is required.'),
            'photo.required'           =>  __('Image field is required.'),
            'photo.mimes'              =>  __('Image type must be jpg,jpeg,png,svg.')
        ];
    }

}
