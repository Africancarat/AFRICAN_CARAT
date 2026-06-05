@extends('master.back')

@section('content')

<div class="container-fluid">

<!-- Page Heading -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>{{ __('Update Product') }}</b> </h3>
            <a class="btn btn-primary   btn-sm" href="{{route('back.item.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
            @include('alerts.alerts')
    </div>
</div>
<!-- Nested Row within Card Body -->

<form class="admin-form" action="{{ route('back.item.update',['item' => $item->id]) }}" method="POST"
    enctype="multipart/form-data">

    @csrf

    @method('PUT')
    <div class="row">

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('Name') }} *</label>
                        <input type="text" name="name" class="form-control item-name"
                            id="name"
                            placeholder="{{ __('Enter Name') }}"
                            value="{{ $item->name }}" >
                    </div>

                    <div class="form-group">
                        <label for="slug">{{ __('Slug') }} *</label>
                        <input type="text" name="slug" class="form-control"
                            id="slug"
                            placeholder="{{ __('Enter Slug') }}"
                            value="{{ $item->slug }}" >
                    </div>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pb-0  mb-0">
                        <label class="d-block">{{ __('Featured Image') }} *</label>
                    </div>
                    <div class="form-group pb-0 pt-0 mt-0 mb-0">
                    <img class="admin-img lg" src="{{ \App\Helpers\ImageHelper::storageImageUrl($item->photo) }}" >
                    </div>
                    <div class="form-group position-relative ">
                        <label class="file">
                            <input type="file"  accept="image/*"   class="upload-photo" name="photo"
                                id="file"  aria-label="File browser example">
                            <span
                                class="file-custom text-left">{{ __('Upload Image...') }}</span>
                        </label>
                        <br>
                        <span class="mt-1 text-info">{{ __('Image Size Should Be 800 x 800. or square size') }}</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pb-0  mb-0">
                        <label>{{ __('Gallery Images') }} </label>
                    </div>
                    <div class="form-group pb-0 pt-0 mt-0 mb-0">
                        <div id="gallery-images">
                            <div class="d-block gallery_image_view">

                                @forelse($item->galleries as $gallery)
                                    <div class="single-g-item d-inline-block m-2">
                                            <span data-toggle="modal"
                                            data-target="#confirm-delete" href="javascript:;"
                                            data-href="{{ route('back.item.gallery.delete',$gallery->id) }}" class="remove-gallery-img">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                            <a class="popup-link" href="{{ \App\Helpers\ImageHelper::storageImageUrl($gallery->photo) }}">
                                                <img class="admin-gallery-img" src="{{ \App\Helpers\ImageHelper::storageImageUrl($gallery->photo) }}"
                                                    alt="No Image Found">
                                            </a>
                                    </div>
                                @empty
                                    <h6><b>{{ __('No Images Added') }}</b></h6>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="form-group position-relative ">
                        <label class="file">
                            <input type="file"  accept="image/*"   name="galleries[]" id="gallery_file"
                                    aria-label="File browser example" accept="image/*" multiple>
                            <span
                                class="file-custom text-left">{{ __('Upload Image...') }}</span>
                        </label>
                        <br>
                        <span class="mt-1 text-info">{{ __('Image Size Should Be 800 x 800. or square size') }}</span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="sort_details">{{ __('Short Description') }} *</label>
                        <textarea name="sort_details" id="sort_details"
                            class="form-control"
                            placeholder="{{ __('Short Description') }}"
                            >{{$item->sort_details}}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="details">{{ __('Description') }} *</label>
                        <textarea name="details" id="details"
                            class="form-control text-editor"
                            rows="6"
                            placeholder="{{ __('Enter Description') }}"
                            >{{$item->details}}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="tags">{{ __('Product Tags') }}
                            </label>
                        <input type="text" name="tags" class="tags"
                            id="tags"
                            placeholder="{{ __('Tags') }}"
                            value="{{$item->tags}}">
                    </div>
                    <div class="form-group">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_specification" value="1" {{$item->is_specification ==1 ? 'checked' : ''}}>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Specifications') }}</span>
                        </label>
                    </div>

                    <div id="specifications-section" class="{{ $item->is_specification == 0 ? 'd-none' : '' }}">
                        @if(!empty($specification_name))
                        @foreach(array_combine($specification_name,$specification_description) as  $name => $description)
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_name[]"
                                        placeholder="{{ __('Specification Name') }}" value="{{$name}}">
                                    </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_description[]"
                                        placeholder="{{ __('Specification description') }}" value="{{$description}}">
                                    </div>
                            </div>
                            <div class="flex-btn">
                                @if($loop->first)
                                <button type="button" class="btn btn-success add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-plus"></i> </button>
                                @else
                                <button type="button" class="btn btn-danger remove-spcification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-minus"></i> </button>
                                @endif
                            </div>
                        </div>

                        @endforeach
                        @else
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_name[]"
                                        placeholder="{{ __('Specification Name') }}" value="">
                                    </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                        name="specification_description[]"
                                        placeholder="{{ __('Specification description') }}" value="">
                                    </div>
                            </div>
                            <div class="flex-btn">
                                <button type="button" class="btn btn-success add-specification" data-text="{{ __('Specification Name') }}" data-text1="{{ __('Specification Description') }}"> <i class="fa fa-plus"></i> </button>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="meta_keywords">{{ __('Meta Keywords') }}
                            </label>
                        <input type="text" name="meta_keywords" class="tags"
                            id="meta_keywords"
                            placeholder="{{ __('Enter Meta Keywords') }}"
                            value="{{ $item->meta_keywords }}">
                    </div>
                    <div class="form-group">
                        <label
                            for="meta_description">{{ __('Meta Description') }}
                            </label>
                        <textarea name="meta_description" id="meta_description"
                            class="form-control" rows="5"
                            placeholder="{{ __('Enter Meta Description') }}">{{ $item->meta_description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" class="check_button" name="is_button" value="0">
                    <button type="submit" class="btn btn-secondary mr-2">{{ __('Update') }}</button>
                    <a class="btn btn-success" href="{{ route('back.attribute.index',$item->id) }}">{{ __('Manage Attributes') }}</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="discount_price">{{ __('Current Price') }}
                            *</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text">{{ $curr->sign }}</span>
                            </div>
                            <input type="text" id="discount_price"
                                name="discount_price" class="form-control"
                                placeholder="{{ __('Enter Current Price') }}"
                                min="1" step="0.1"
                                value="{{ round($item->discount_price * $curr->value,2) }}" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="previous_price">{{ __('Previous Price') }}
                            </label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span
                                    class="input-group-text">{{ $curr->sign }}</span>
                            </div>
                            <input type="text" id="previous_price"
                                name="previous_price" class="form-control"
                                placeholder="{{ __('Enter Previous Price') }}"
                                min="1" step="0.1"
                                value="{{ round($item->previous_price*$curr->value ,2)}}" >
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted text-uppercase small mb-3">{{ __('Jewelry cost') }}</h6>
                    <div class="form-group">
                        <label for="gold_weight">{{ __('Gold weight (g)') }}</label>
                        <input type="number" id="gold_weight" name="gold_weight" class="form-control"
                            step="0.001" min="0"
                            value="{{ old('gold_weight', $item->gold_weight ?? '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="labour_per_gram">{{ __('Labour per gram') }}</label>
                        <input type="number" id="labour_per_gram" name="labour_per_gram" class="form-control"
                            step="0.01" min="0"
                            value="{{ old('labour_per_gram', $item->labour_per_gram ?? '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="igi_per_carat">{{ __('IGI per carat') }}</label>
                        <input type="number" id="igi_per_carat" name="igi_per_carat" class="form-control"
                            step="0.01" min="0"
                            value="{{ old('igi_per_carat', $item->igi_per_carat ?? '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="margin_type">{{ __('Margin type') }}</label>
                        <select name="margin_type" id="margin_type" class="form-control">
                            @php $mt = old('margin_type', $item->margin_type ?? 'percent'); @endphp
                            <option value="percent" {{ $mt == 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
                            <option value="fixed" {{ $mt == 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="margin_value">{{ __('Margin value') }}</label>
                        <input type="number" id="margin_value" name="margin_value" class="form-control"
                            step="0.01" min="0"
                            value="{{ old('margin_value', $item->margin_value ?? '0') }}">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="category_id">{{ __('Select Category') }} *</label>
                        <select name="category_id" id="category_id" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                            <option value="{{ $cat->id }}" {{ $cat->id == $item->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subcategory_id">{{ __('Select Sub Category') }} </label>
                        <select name="subcategory_id" id="subcategory_id" class="form-control" data-href="{{route('back.get.childcategory')}}">
                            <option value="">{{__('Select one')}}</option>
                            @foreach(DB::table('subcategories')->where('category_id',$item->category_id)->whereStatus(1)->get() as $subcat)
                            <option value="{{ $subcat->id }}" {{ $subcat->id == $item->subcategory_id ? 'selected' : '' }}>{{ $subcat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="childcategory_id">{{ __('Select Child Category') }} </label>
                        <select name="childcategory_id" id="childcategory_id" class="form-control">
                            <option value="">{{__('Select one')}}</option>
                            @foreach(DB::table('chield_categories')->where('category_id',$item->category_id)->whereStatus(1)->get() as $chieldcategory)
                            <option value="{{ $chieldcategory->id }}" {{ $chieldcategory->id == $item->childcategory_id ? 'selected' : '' }}>{{ $chieldcategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brand_id">{{ __('Select Brand') }} </label>
                        <select name="brand_id" id="brand_id" class="form-control" >
                            <option value="" selected>{{__('Select Brand')}}</option>
                            @foreach(DB::table('brands')->whereStatus(1)->get() as $brand)
                            <option value="{{ $brand->id }}" {{$brand->id == $item->brand_id ? 'selected' : ''}} >{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    @php
                        $metalOld = old('metal_type');
                        if (is_array($metalOld)) {
                            $metalSelected = $metalOld;
                        } else {
                            $metalSelected = is_array($item->metal_type)
                                ? $item->metal_type
                                : (\App\Models\Item::normalizeJewelryOptionList($item->metal_type) ?? []);
                        }
                        $karatOld = old('gold_karat');
                        if (is_array($karatOld)) {
                            $karatSelected = $karatOld;
                        } else {
                            $karatSelected = is_array($item->gold_karat)
                                ? $item->gold_karat
                                : (\App\Models\Item::normalizeJewelryOptionList($item->gold_karat) ?? []);
                        }
                    @endphp
                    <div class="form-group">
                        <label for="metal_type">{{ __('Metal Type') }}</label>
                        <select name="metal_type[]" id="metal_type" class="form-control" multiple size="4">
                            @foreach (['YELLOW GOLD', 'ROSE GOLD', 'WHITE GOLD'] as $m)
                                <option value="{{ $m }}" {{ in_array($m, $metalSelected, true) ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                            @foreach ($metalSelected as $sel)
                                @if (!in_array($sel, ['YELLOW GOLD', 'ROSE GOLD', 'WHITE GOLD'], true))
                                    <option value="{{ $sel }}" selected>{{ $sel }}</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">
                            {{ __('Click options to toggle selection (no Ctrl/Cmd).') }}
                            <span id="metal_type_selection_count" class="text-dark font-weight-bold"></span>
                            {{ __('Stored as JSON.') }}
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="gold_karat">{{ __('Gold Karat') }}</label>
                        <select name="gold_karat[]" id="gold_karat" class="form-control" multiple size="4">
                            @foreach (['14KT', '18KT', '22KT', '24KT', 'SILVER', 'PLATINUM'] as $g)
                                <option value="{{ $g }}" {{ in_array($g, $karatSelected, true) ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                            @foreach ($karatSelected as $sel)
                                @if (!in_array($sel, ['14KT', '18KT', '22KT', '24KT', 'SILVER', 'PLATINUM'], true))
                                    <option value="{{ $sel }}" selected>{{ $sel }}</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">
                            {{ __('Click options to toggle selection (no Ctrl/Cmd).') }}
                            <span id="gold_karat_selection_count" class="text-dark font-weight-bold"></span>
                            {{ __('Stored as JSON.') }}
                        </small>
                    </div>

                    @php
                        $existingMetalVariants = is_array($item->pdp_metal_variants) ? $item->pdp_metal_variants : [];
                        $variantImgByKey = [];
                        foreach ($existingMetalVariants as $row) {
                            if (! is_array($row)) continue;
                            $k = strtoupper((string) ($row['key'] ?? $row['label'] ?? $row['slug'] ?? ''));
                            $img = (string) ($row['image'] ?? $row['photo'] ?? '');
                            if ($k && $img) $variantImgByKey[$k] = $img;
                        }
                    @endphp
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="mb-2"><b>{{ __('Metal images (PDP)') }}</b></h6>
                            <p class="small text-muted mb-3">
                                {{ __('Optional. Upload one image per metal type to enable metal-based image switching on the product page.') }}
                            </p>

                            <div class="form-group">
                                <label class="d-block">{{ __('Yellow Gold Images') }}</label>
                                <div class="small text-muted mb-2">
                                    {{ __('Automatically uses Featured image + Gallery images as Yellow Gold.') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="d-block">{{ __('Rose Gold Image') }}</label>
                                @if (! empty($variantImgByKey['ROSE GOLD'] ?? null))
                                    <div class="mb-2">
                                        <img style="max-width:140px;border:1px solid #eee;" src="{{ url('/core/public/storage/images/'.$variantImgByKey['ROSE GOLD']) }}" alt="">
                                    </div>
                                @endif
                                <input type="file" name="pdp_metal_images_rose[]" accept="image/*" class="form-control" multiple>
                            </div>
                            <div class="form-group mb-0">
                                <label class="d-block">{{ __('White Gold Image') }}</label>
                                @if (! empty($variantImgByKey['WHITE GOLD'] ?? null))
                                    <div class="mb-2">
                                        <img style="max-width:140px;border:1px solid #eee;" src="{{ url('/core/public/storage/images/'.$variantImgByKey['WHITE GOLD']) }}" alt="">
                                    </div>
                                @endif
                                <input type="file" name="pdp_metal_images_white[]" accept="image/*" class="form-control" multiple>
                            </div>
                        </div>
                    </div>

                    @php
                        $da = $item->diamondAttribute;
                        $hasDiamond = (bool) old('has_diamond', $da ? 1 : 0);
                    @endphp

                    <hr>
                    <div class="form-group mb-2">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap radio-check" name="has_diamond" value="1"
                                {{ $hasDiamond ? 'checked' : '' }} id="has_diamond_toggle">
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Has Diamond') }}</span>
                        </label>
                    </div>

                    <div id="diamond_details_box" class="{{ $hasDiamond ? '' : 'd-none' }}">
                        <h6 class="mb-2"><b>{{ __('Diamond Details') }}</b></h6>
                        @php
                            $colorOld = old('color_grade');
                            if (is_array($colorOld)) {
                                $colorSelected = $colorOld;
                            } else {
                                $colorSelected = is_array($da?->color_grade)
                                    ? $da->color_grade
                                    : (\App\Models\Item::normalizeJewelryOptionList($da?->color_grade ?? null) ?? []);
                            }

                            $clarityOld = old('clarity_grade');
                            if (is_array($clarityOld)) {
                                $claritySelected = $clarityOld;
                            } else {
                                $claritySelected = is_array($da?->clarity_grade)
                                    ? $da->clarity_grade
                                    : (\App\Models\Item::normalizeJewelryOptionList($da?->clarity_grade ?? null) ?? []);
                            }
                        @endphp

                        <div class="form-group">
                            <label for="carat_weight">{{ __('Carat Weight') }}</label>
                            <input type="number" step="0.001" name="carat_weight" id="carat_weight" class="form-control"
                                value="{{ old('carat_weight', $da->carat_weight ?? '') }}" placeholder="{{ __('e.g. 1.25') }}">
                        </div>

                        <div class="form-group">
                            <label for="shape">{{ __('Shape') }}</label>
                            <select name="shape" id="shape" class="form-control">
                                <option value="">{{ __('Select One') }}</option>
                                @foreach (['Round','Princess','Oval','Cushion','Radiant','Pear','Emerald','Asscher','Marquise','Heart'] as $s)
                                    <option value="{{ $s }}" {{ old('shape', $da->shape ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cut_grade">{{ __('Cut Grade') }}</label>
                            <select name="cut_grade" id="cut_grade" class="form-control">
                                <option value="">{{ __('Select One') }}</option>
                                @foreach (['Excellent','Very Good','Good','Fair'] as $g)
                                    <option value="{{ $g }}" {{ old('cut_grade', $da->cut_grade ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="color_grade">{{ __('Color Grade') }}</label>
                            <select name="color_grade[]" id="color_grade" class="form-control" multiple size="5">
                                @foreach (['D','E','F','G','H','I','J'] as $g)
                                    <option value="{{ $g }}" {{ in_array($g, $colorSelected, true) ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                                @foreach ($colorSelected as $sel)
                                    @if (!in_array($sel, ['D','E','F','G','H','I','J'], true))
                                        <option value="{{ $sel }}" selected>{{ $sel }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                {{ __('Click options to toggle selection (no Ctrl/Cmd).') }}
                                <span id="color_grade_selection_count" class="text-dark font-weight-bold"></span>
                                {{ __('Stored as JSON.') }}
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="clarity_grade">{{ __('Clarity Grade') }}</label>
                            <select name="clarity_grade[]" id="clarity_grade" class="form-control" multiple size="5">
                                @foreach (['FL','IF','VVS1','VVS2','VS1','VS2','SI1','SI2'] as $g)
                                    <option value="{{ $g }}" {{ in_array($g, $claritySelected, true) ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                                @foreach ($claritySelected as $sel)
                                    @if (!in_array($sel, ['FL','IF','VVS1','VVS2','VS1','VS2','SI1','SI2'], true))
                                        <option value="{{ $sel }}" selected>{{ $sel }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                {{ __('Click options to toggle selection (no Ctrl/Cmd).') }}
                                <span id="clarity_grade_selection_count" class="text-dark font-weight-bold"></span>
                                {{ __('Stored as JSON.') }}
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="lab">{{ __('Lab') }}</label>
                            <select name="lab" id="lab" class="form-control">
                                <option value="">{{ __('Select One') }}</option>
                                @foreach (['IGI','GIA','HRD','AGS','Other'] as $l)
                                    <option value="{{ $l }}" {{ old('lab', $da->lab ?? '') === $l ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="certificate_number">{{ __('Certificate Number') }}</label>
                            <input type="text" name="certificate_number" id="certificate_number" class="form-control"
                                value="{{ old('certificate_number', $da->certificate_number ?? '') }}" placeholder="{{ __('e.g. IGI-123456789') }}">
                        </div>

                        <div class="form-group">
                            <label for="video_360_url">{{ __('Video 360 URL') }}</label>
                            <input type="text" name="video_360_url" id="video_360_url" class="form-control"
                                value="{{ old('video_360_url', $da->video_360_url ?? '') }}" placeholder="{{ __('https://...') }}">
                        </div>

                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="is_lab_grown" name="is_lab_grown" value="1"
                                    {{ old('is_lab_grown', ($da->is_lab_grown ?? false) ? 1 : 0) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_lab_grown">{{ __('Lab grown') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="stock">{{ __('Total in stock') }}
                            *</label>
                        <div class="input-group mb-3">
                            <input type="number" id="stock"
                                name="stock" class="form-control"
                                placeholder="{{ __('Total in stock') }}" value="{{$item->stock}}" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tax_id">{{ __('Select Tax') }} *</label>
                        <select name="tax_id" id="tax_id" class="form-control">
                            <option value="">{{__('Select One')}}</option>
                            @foreach(DB::table('taxes')->whereStatus(1)->get() as $tax)
                            <option value="{{ $tax->id }}" {{$item->tax_id == $tax->id ? 'selected' : ''}} >{{ $tax->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sku">{{ __('SKU') }} *</label>
                        <input type="text" name="sku" class="form-control"
                            id="sku" placeholder="{{ __('Enter SKU') }}"
                            value="{{$item->sku}}" >
                    </div>
                    <div class="form-group">
                        <label for="video">{{ __('Video Link') }} </label>
                        <input type="text" name="video" class="form-control"
                            id="video" placeholder="{{ __('Enter Video Link') }}"
                            value="{{$item->video}}" >
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
</div>

<script>
    (function () {
        var t = document.getElementById('has_diamond_toggle');
        var box = document.getElementById('diamond_details_box');
        if (!t || !box) return;
        t.addEventListener('change', function () {
            if (this.checked) {
                box.classList.remove('d-none');
            } else {
                box.classList.add('d-none');
            }
        });
    })();
</script>
<script>
    (function () {
        function countSelected(select) {
            var n = 0;
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].selected) {
                    n++;
                }
            }
            return n;
        }

        var selectedWord = @json(__('selected'));

        function updateCountEl(spanId, n) {
            var el = document.getElementById(spanId);
            if (!el) {
                return;
            }
            el.textContent = ' (' + n + ' ' + selectedWord + ')';
        }

        function bindJewelryMultiSelect(selectId, countSpanId) {
            var sel = document.getElementById(selectId);
            if (!sel || !sel.multiple) {
                return;
            }

            function refresh() {
                updateCountEl(countSpanId, countSelected(sel));
            }

            sel.addEventListener('mousedown', function (e) {
                var opt = e.target;
                if (!opt || opt.tagName !== 'OPTION') {
                    return;
                }
                e.preventDefault();
                opt.selected = !opt.selected;
                if (typeof sel.focus === 'function') {
                    sel.focus();
                }
                refresh();
                sel.dispatchEvent(new Event('change', { bubbles: true }));
            });

            sel.addEventListener('change', refresh);
            refresh();
        }

        bindJewelryMultiSelect('metal_type', 'metal_type_selection_count');
        bindJewelryMultiSelect('gold_karat', 'gold_karat_selection_count');
        bindJewelryMultiSelect('color_grade', 'color_grade_selection_count');
        bindJewelryMultiSelect('clarity_grade', 'clarity_grade_selection_count');
    })();
</script>
{{-- DELETE MODAL --}}

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

		<!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('Confirm Delete?') }}</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
		</div>

		<!-- Modal Body -->
        <div class="modal-body">
			{{ __('You are going to delete this image from gallery.') }} {{ __('Do you want to delete it?') }}
		</div>

		<!-- Modal footer -->
        <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
			<form action="" class="d-inline btn-ok" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
			</form>
		</div>

      </div>
    </div>
  </div>

{{-- DELETE MODAL ENDS --}}

@endsection
