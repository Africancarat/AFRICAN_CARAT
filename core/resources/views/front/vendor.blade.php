@extends('master.front')
@section('meta')
<meta name="keywords" content="{{$setting->meta_keywords}}">
<meta name="description" content="{{$setting->meta_description}}">
@endsection
@section('title')
    {{__('Contact')}}
@endsection

@section('content')
    <section class="store-hero-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="shop-banner-content">
                        <img class="shop-banner-logo" src="https://shofy.botble.com/storage/main/stores/8.png" alt="Old El Paso">
                        <div class="shop-banner-info">
                            <h2 class="shop-banner-name">Old El Paso</h2>
                            <div class="shop-banner-contact">
                                <div class="shop-banner-address d-flex gap-1">
                                    <svg class="icon svg-icon-ti-ti-map-pin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z">
                                        </path>
                                    </svg> 562 Jakob Manors, East Selenaton, Michigan, TR
                                </div>
                                <div class="shop-banner-phone d-flex gap-1"><svg class="icon svg-icon-ti-ti-phone" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2">
                                        </path>
                                    </svg><a href="tel:+19594255150">+19594255150</a>
                                </div>
                                <div class="shop-banner-address d-flex gap-1"><svg class="icon svg-icon-ti-ti-mail" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z">
                                        </path>
                                        <path d="M3 7l9 6l9 -6"></path>
                                    </svg><a href="mailto:anderson.berta@example.net">anderson.berta@example.net</a>
                                </div>
                            </div>
                            <div class="shop-banner-description ck-content"> Odio nihil quam illo fuga veritatis deserunt
                                praesentium. Expedita omnis blanditiis tempora earum maxime saepe cumque. Quisquam sint ipsa
                                aliquid vel. Quia commodi corrupti ab necessitatibus adipisci nam. Sint assumenda modi eos
                                aliquam. Aut asperiores rem temporibus sunt consectetur iusto cumque. Libero commodi optio
                                eos laudantium velit quasi id perspiciatis. </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
  <!-- Page Content-->
  <div class="container  mt-30">
    <div class="row">
        <div class="col-lg-12">
            <div class="shop-top-filter-wrapper">
                <div class="row">
                    <div class="col-md-10 gd-text-sm-center">
                        <div class="sptfl">
                            <div class="quickFilter">
                                <h4 class="quickFilter-title"><i class="fas fa-filter"></i>{{__('Quick filter')}}</h4>
                                <ul id="quick_filter">
                                    <li><a datahref=""><i class="icon-chevron-right pr-2"></i>{{__('All products')}} </a></li>
                                    <li class=""><a href="javascript:;" data-href="feature"><i class="icon-chevron-right pr-2"></i>{{__('Featured products')}} </a></li>
                                    <li class=""><a href="javascript:;" data-href="best"><i class="icon-chevron-right pr-2"></i>{{__('Best sellers')}} </a></li>
                                    <li class=""><a href="javascript:;" data-href="top"><i class="icon-chevron-right pr-2"></i>{{__('Top rated')}} </a></li>
                                    <li class=""><a href="javascript:;" data-href="new"><i class="icon-chevron-right pr-2"></i>{{__('New Arrival')}} </a></li>
                                </ul>
                            </div>
                            <div class="shop-sorting">
                                <label for="sorting">{{__('Sort by')}}:</label>
                                <select class="form-control" id="sorting">
                                <option value="">{{__('All Products')}}</option>
                                <option value="low_to_high" {{request()->input('low_to_high') ? 'selected' : ''}}>{{__('Low - High Price')}}</option>
                                <option value="high_to_low" {{request()->input('high_to_low') ? 'selected' : ''}}>{{__('High - Low Price')}}</option>
                                </select><span class="text-muted">{{__('Showing')}}:</span><span>1 - {{$setting->view_product}} {{__('items')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 gd-text-sm-center">
                        <div class="shop-view"><a class="list-view {{Session::has('view_catalog') && Session::get('view_catalog') == 'grid' ? 'active' : ''}} " data-step="grid" href="javascript:;" data-href="{{route('front.catalog').'?view_check=grid'}}"><i class="fas fa-th-large"></i></a>
                            <a class="list-view {{Session::has('view_catalog') && Session::get('view_catalog') == 'list' ? 'active' : ''}}" href="javascript:;" data-step="list" data-href="{{route('front.catalog').'?view_check=list'}}"><i class="fas fa-list"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3">

      <div class="col-lg-9 order-lg-2" id="list_view_ajax">
        @include('front.catalog.catalog')
      </div>

      <!-- Sidebar          -->
      <div class="col-lg-3 order-lg-1">
        <div class="sidebar-toggle position-left"><i class="icon-filter"></i></div>
        <aside class="sidebar sidebar-offcanvas position-left"><span class="sidebar-close"><i class="icon-x"></i></span>
          <!-- Widget Categories-->
          <section class="widget widget-categories card rounded p-4">
            <h3 class="widget-title">{{__('Shop Categories')}}</h3>
            <ul id="category_list" class="category-scroll">
                @foreach ($categories as $getcategory)
                <li class="has-children  {{isset($category) && $category->id == $getcategory->id ? 'expanded active' : ''}} ">
                  <a class="category_search" href="javascript:;"  data-href="{{$getcategory->slug}}">{{$getcategory->name}}</a>

                    <ul id="subcategory_list">
                        @foreach ($getcategory->subcategory as $getsubcategory)
                        <li class="{{isset($subcategory) && $subcategory->id == $getsubcategory->id ? 'active' : ''}}">
                          <a class="subcategory" href="javascript:;" data-href="{{$getsubcategory->slug}}">{{$getsubcategory->name}}</a>

                          <ul id="childcategory_list">
                            @foreach ($getsubcategory->childcategory as $getchildcategory)
                            <li class="{{isset($childcategory) && $getchildcategory->id == $getchildcategory->id ? 'active' : ''}}">
                              <a class="childcategory" href="javascript:;" data-href="{{$getchildcategory->slug}}">{{$getchildcategory->name}}</a>

                            </li>
                            @endforeach
                        </ul>
                        </li>
                        @endforeach
                    </ul>
                  </li>
                @endforeach
            </ul>
          </section>

          @if ($setting->is_range_search == 1)
               <!-- Widget Price Range-->
          <section class="widget widget-categories card rounded p-4">
            <h3 class="widget-title">{{ __('Filter by Price') }}</h3>
            <form class="price-range-slider" method="post" data-start-min="{{request()->input('minPrice') ? request()->input('minPrice') : '0'}}" data-start-max="{{request()->input('maxPrice') ? request()->input('maxPrice') : $setting->max_price}}" data-min="0" data-max="{{$setting->max_price}}" data-step="5">
              <div class="ui-range-slider"></div>
              <footer class="ui-range-slider-footer">
                <div class="column">
                  <button class="btn btn-primary btn-sm" id="price_filter" type="button"><span>{{__('Filter')}}</span></button>
                </div>
                <div class="column">
                  <div class="ui-range-values">
                    <div class="ui-range-value-min">{{PriceHelper::setCurrencySign()}}<span class="min_price"></span>
                      <input type="hidden">
                    </div>-
                    <div class="ui-range-value-max">{{PriceHelper::setCurrencySign()}}<span class="max_price"></span>
                      <input type="hidden">
                    </div>
                  </div>
                </div>
              </footer>
            </form>
          </section>
          @endif

          @if ($setting->is_attribute_search == 1)
          @foreach ($attrubutes as $attrubute)
          
          <section class="widget widget-categories card rounded p-4">
            <h3 class="widget-title">{{ __('Filter by') }} {{$attrubute->name}}</h3>
            @foreach ($options as $option)
            @if ($attrubute->keyword == $option->attribute->keyword)
            <div class="custom-control custom-checkbox">
              <input class="custom-control-input option" {{isset($subcategory) && $subcategory->id == $option->id ? 'checked' : ''}}   type="checkbox" value="{{$option->name}}" id="{{$attrubute->id}}{{$option->name}}">
              <label class="custom-control-label" for="{{$attrubute->id}}{{$option->name}}">{{$option->name}}<span class="text-muted"></span></label>
          </div>  
            @endif
            @endforeach
          </section>
          @endforeach
          @endif

          <!-- Widget Brand Filter-->
          <section class="widget widget-categories card rounded p-4">
            <h3 class="widget-title">{{__('Filter by Brand')}}</h3>
            <div class="custom-control custom-checkbox">
              <input class="custom-control-input brand-select" type="checkbox" value="" id="all-brand">
              <label class="custom-control-label" for="all-brand">{{__('All Brands')}}</label>
            </div>
            @foreach ($brands as $getbrand)
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input brand-select" {{isset($brand) && $brand->id == $getbrand->id ? 'checked' : ''}} type="checkbox" value="{{$getbrand->slug}}" id="{{$getbrand->slug}}">
                <label class="custom-control-label" for="{{$getbrand->slug}}">{{$getbrand->name}}</label>
              </div>
            @endforeach
          </section>


        </aside>
      </div>
    </div>
  </div>



  <form id="search_form" class="d-none" action="{{route('front.catalog')}}" method="GET">

    <input type="text" name="maxPrice" id="maxPrice" value="{{request()->input('maxPrice') ? request()->input('maxPrice') : ''}}">
    <input type="text" name="minPrice" id="minPrice" value="{{request()->input('minPrice') ? request()->input('minPrice') : ''}}">
    <input type="text" name="brand" id="brand" value="{{isset($brand) ? $brand->slug : ''}}">
    <input type="text" name="brand" id="brand" value="{{isset($brand) ? $brand->slug : ''}}">
    <input type="text" name="category" id="category" value="{{isset($category) ? $category->slug : ''}}">
    <input type="text" name="quick_filter" id="quick_filter" value="">
    <input type="text" name="childcategory" id="childcategory" value="{{isset($childcategory) ? $childcategory->slug : ''}}">
    <input type="text" name="page" id="page" value="{{isset($page) ? $page : ''}}">
    <input type="text" name="attribute" id="attribute" value="{{isset($attribute) ? $attribute : ''}}">
    <input type="text" name="option" id="option" value="{{isset($option) ? $option : ''}}">
    <input type="text" name="subcategory" id="subcategory" value="{{isset($subcategory) ? $subcategory->slug : ''}}">
    <input type="text" name="sorting" id="sorting" value="{{isset($sorting) ? $sorting : ''}}">
    <input type="text" name="view_check" id="view_check" value="{{isset($view_check) ? $view_check : ''}}">


    <button type="submit" id="search_button" class="d-none"></button>
</form>
@endsection
