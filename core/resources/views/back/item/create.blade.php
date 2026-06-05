@extends('master.back')

@section('content')

<div class="container-fluid">

<!-- Page Heading -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h3 class="mb-0 bc-title"><b>{{ __('Create Product') }}</b> </h3>
            <a class="btn btn-primary   btn-sm" href="{{route('back.item.index')}}"><i class="fas fa-chevron-left"></i> {{ __('Back') }}</a>
        </div>
    </div>
</div>

<!-- Form -->


<div class="row">
    <div class="col-lg-12">
            @include('alerts.alerts')
    </div>
</div>
<!-- Nested Row within Card Body -->
<form class="admin-form tab-form" action="{{ route('back.item.store') }}" method="POST"
                enctype="multipart/form-data">
                <input type="hidden" value="normal" name="item_type">
                @csrf
    <div class="row">

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('Name') }} *</label>
                        <input type="text" name="name" class="form-control item-name"
                            id="name" placeholder="{{ __('Enter Name') }}"
                            value="{{ old('name') }}" >
                    </div>
                    <div class="form-group">
                        <label for="slug">{{ __('Slug') }} *</label>
                        <input type="text" name="slug" class="form-control"
                            id="slug" placeholder="{{ __('Enter Slug') }}"
                            value="{{ old('slug') }}" >
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group pb-0  mb-0">
                        <label class="d-block">{{ __('Featured Image') }} *</label>
                    </div>
                    <div class="form-group pb-0 pt-0 mt-0 mb-0">
                    <img class="admin-img lg" src="" >
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
                        <div id="gallery-images" class="">
                            <div class="d-block gallery_image_view">
                            </div>
                        </div>
                    </div>
                    <div class="form-group position-relative ">
                        <label class="file">
                            <input type="file"  accept="image/*"  name="galleries[]" id="gallery_file" aria-label="File browser example" accept="image/*" multiple>
                            <span class="file-custom text-left">{{ __('Upload Image...') }}</span>
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
                            >{{ old('sort_details') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="details">{{ __('Description') }} *</label>
                        <textarea name="details" id="details"
                            class="form-control text-editor"
                            rows="6"
                            placeholder="{{ __('Enter Description') }}"
                            >{{ old('details') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label for="tags">{{ __('Product Tags') }}
                            </label>
                        <input type="text" name="tags" class="tags"
                            id="tags"
                            placeholder="{{ __('Tags') }}"
                            value="">
                    </div>
                    <div class="form-group">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap status radio-check" name="is_specification" value="1" checked>
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Specifications') }}</span>
                        </label>
                    </div>
                    <div id="specifications-section">
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
                            value="">
                    </div>

                    <div class="form-group">
                        <label
                            for="meta_description">{{ __('Meta Description') }}
                            </label>
                        <textarea name="meta_description" id="meta_description"
                            class="form-control" rows="5"
                            placeholder="{{ __('Enter Meta Description') }}"
                        >{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" class="check_button" name="is_button" value="0">
                    <button type="submit" class="btn btn-secondary mr-2">{{ __('Save') }}</button>
                    <button type="submit" class="btn btn-info save__edit">{{ __('Save & Edit') }}</button>
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
                                    class="input-group-text">{{ PriceHelper::adminCurrency() }}</span>
                            </div>
                            <input type="text" id="discount_price"
                                name="discount_price" class="form-control"
                                placeholder="{{ __('Enter Current Price') }}"
                                min="1" step="0.1"
                                value="{{ old('discount_price') }}" >
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
                                value="{{ old('previous_price') }}" >
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted text-uppercase small mb-3">{{ __('Jewelry cost') }}</h6>
                    <div class="form-group">
                        <label for="gold_weight">{{ __('Gold weight (g)') }}</label>
                        <input type="number" id="gold_weight" name="gold_weight" class="form-control"
                            step="0.001" min="0"
                            value="{{ old('gold_weight', '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="labour_per_gram">{{ __('Labour per gram') }}</label>
                        <input type="number" id="labour_per_gram" name="labour_per_gram" class="form-control"
                            step="0.01" min="0"
                            value="{{ old('labour_per_gram', '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="igi_per_carat">{{ __('IGI per carat') }}</label>
                        <input type="number" id="igi_per_carat" name="igi_per_carat" class="form-control"
                            step="0.01" min="0"
                            value="{{ old('igi_per_carat', '0') }}">
                    </div>
                    <div class="form-group">
                        <label for="margin_type">{{ __('Margin type') }}</label>
                        <select name="margin_type" id="margin_type" class="form-control">
                            <option value="percent" {{ old('margin_type', 'percent') == 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
                            <option value="fixed" {{ old('margin_type') == 'fixed' ? 'selected' : '' }}>{{ __('Fixed') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="margin_value">{{ __('Margin value') }}</label>
                        <input type="number" id="margin_value" name="margin_value" class="form-control"
                            step="0.01" min="0"
                            value="{{ old('margin_value', '0') }}">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                    <div class="form-group">
                        <label for="category_id">{{ __('Select Category') }} *</label>
                        <select name="category_id" id="category_id" data-href="{{route('back.get.subcategory')}}" class="form-control" >
                            <option value="" selected>{{__('Select One')}}</option>
                            @foreach(DB::table('categories')->whereStatus(1)->get() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subcategory_id">{{ __('Select Sub Category') }} </label>
                        <select name="subcategory_id" id="subcategory_id" data-href="{{route('back.get.childcategory')}}" class="form-control">
                            <option value="">{{__('Select One')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="childcategory_id">{{ __('Select Child Category') }} </label>
                        <select name="childcategory_id" id="childcategory_id" class="form-control">
                            <option value="">{{__('Select One')}}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="brand_id">{{ __('Select Brand') }} </label>
                        <select name="brand_id" id="brand_id" class="form-control" >
                            <option value="" selected>{{__('Select Brand')}}</option>
                            @foreach(DB::table('brands')->whereStatus(1)->get() as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    @php
                        $metalOld = old('metal_type');
                        $metalSelected = is_array($metalOld) ? $metalOld : [];
                        $karatOld = old('gold_karat');
                        $karatSelected = is_array($karatOld) ? $karatOld : [];
                    @endphp
                    <div class="form-group">
                        <label for="metal_type">{{ __('Metal Type') }}</label>
                        <select name="metal_type[]" id="metal_type" class="form-control" multiple size="4">
                            @foreach (['YELLOW GOLD', 'ROSE GOLD', 'WHITE GOLD'] as $m)
                                <option value="{{ $m }}" {{ in_array($m, $metalSelected, true) ? 'selected' : '' }}>{{ $m }}</option>
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
                        </select>
                        <small class="text-muted d-block mt-1">
                            {{ __('Click options to toggle selection (no Ctrl/Cmd).') }}
                            <span id="gold_karat_selection_count" class="text-dark font-weight-bold"></span>
                            {{ __('Stored as JSON.') }}
                        </small>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="mb-2"><b>{{ __('Metal images (PDP)') }}</b></h6>
                            <p class="small text-muted mb-3">
                                {{ __('Optional. Upload one image per metal type to enable metal-based image switching on the product page.') }}
                            </p>

                            <div class="form-group">
                                <label class="d-block">{{ __('Yellow Gold Images') }}</label>
                                <div class="small text-muted">
                                    {{ __('Automatically uses Featured image + Gallery images as Yellow Gold.') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="d-block">{{ __('Rose Gold Image') }}</label>
                                <input type="file" name="pdp_metal_images_rose[]" accept="image/*" class="form-control" multiple>
                            </div>
                            <div class="form-group mb-0">
                                <label class="d-block">{{ __('White Gold Image') }}</label>
                                <input type="file" name="pdp_metal_images_white[]" accept="image/*" class="form-control" multiple>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group mb-2">
                        <label class="switch-primary">
                            <input type="checkbox" class="switch switch-bootstrap radio-check" name="has_diamond" value="1"
                                {{ old('has_diamond') ? 'checked' : '' }} id="has_diamond_toggle">
                            <span class="switch-body"></span>
                            <span class="switch-text">{{ __('Has Diamond') }}</span>
                        </label>
                    </div>

                    <div id="diamond_details_box" class="{{ old('has_diamond') ? '' : 'd-none' }}">
                        <h6 class="mb-2"><b>{{ __('Diamond Details') }}</b></h6>
                        @php
                            $colorOld = old('color_grade');
                            $colorSelected = is_array($colorOld) ? $colorOld : [];
                            $clarityOld = old('clarity_grade');
                            $claritySelected = is_array($clarityOld) ? $clarityOld : [];
                        @endphp

                        <div class="form-group">
                            <label for="carat_weight">{{ __('Carat Weight') }}</label>
                            <input type="number" step="0.001" name="carat_weight" id="carat_weight" class="form-control"
                                value="{{ old('carat_weight') }}" placeholder="{{ __('e.g. 1.25') }}">
                        </div>

                        <div class="form-group">
                            <label for="shape">{{ __('Shape') }}</label>
                            <select name="shape" id="shape" class="form-control">
                                <option value="">{{ __('Select One') }}</option>
                                @foreach (['Round','Princess','Oval','Cushion','Radiant','Pear','Emerald','Asscher','Marquise','Heart'] as $s)
                                    <option value="{{ $s }}" {{ old('shape') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cut_grade">{{ __('Cut Grade') }}</label>
                            <select name="cut_grade" id="cut_grade" class="form-control">
                                <option value="">{{ __('Select One') }}</option>
                                @foreach (['Excellent','Very Good','Good','Fair'] as $g)
                                    <option value="{{ $g }}" {{ old('cut_grade') === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="color_grade">{{ __('Color Grade') }}</label>
                            <select name="color_grade[]" id="color_grade" class="form-control" multiple size="5">
                                @foreach (['D','E','F','G','H','I','J'] as $g)
                                    <option value="{{ $g }}" {{ in_array($g, $colorSelected, true) ? 'selected' : '' }}>{{ $g }}</option>
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
                                    <option value="{{ $l }}" {{ old('lab') === $l ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="certificate_number">{{ __('Certificate Number') }}</label>
                            <input type="text" name="certificate_number" id="certificate_number" class="form-control"
                                value="{{ old('certificate_number') }}" placeholder="{{ __('e.g. IGI-123456789') }}">
                        </div>

                        <div class="form-group">
                            <label for="video_360_url">{{ __('Video 360 URL') }}</label>
                            <input type="text" name="video_360_url" id="video_360_url" class="form-control"
                                value="{{ old('video_360_url') }}" placeholder="{{ __('https://...') }}">
                        </div>

                        <div class="form-group mb-0">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="is_lab_grown" name="is_lab_grown" value="1"
                                    {{ old('is_lab_grown') ? 'checked' : '' }}>
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
                                placeholder="{{ __('Total in stock') }}" value="{{ old('stock') }}" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tax_id">{{ __('Select Tax') }} *</label>
                        <select name="tax_id" id="tax_id" class="form-control">
                            <option value="">{{__('Select One')}}</option>
                            @foreach(DB::table('taxes')->whereStatus(1)->get() as $tax)
                            <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sku">{{ __('SKU') }} *</label>
                        <input type="text" name="sku" class="form-control"
                            id="sku" placeholder="{{ __('Enter SKU') }}"
                            value="{{Str::random(10)}}" >
                    </div>
                    <div class="form-group">
                        <label for="video">{{ __('Video Link') }} </label>
                        <input type="text" name="video" class="form-control"
                            id="video" placeholder="{{ __('Enter Video Link') }}"
                            value="{{ old('video') }}">
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

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


</div>

</div>

@endsection
