    
    @include('components.trust-strip')

    <!-- Header-->

    <header class="site-header navbar-sticky">
        
        <div class="menu-top-area d-lg-none">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="t-m-s-a">
                            <a class="track-order-link" href="{{ route('front.order.track') }}"><i
                                    class="icon-map-pin"></i>{{ __('Track Order') }}</a>
                            <a class="track-order-link compare-mobile d-lg-none"
                                href="{{ route('diamonds.compare.index') }}">{{ __('Compare') }}</a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="right-area">

                            <a class="track-order-link wishlist-mobile d-inline-block d-lg-none"
                                href="{{ route('user.wishlist.index') }}"><i
                                    class="icon-heart"></i>{{ __('Wishlist') }}</a>

                            <div class="t-h-dropdown ">
                                <a class="main-link" href="#">{{ __('Currency') }}<i
                                        class="icon-chevron-down"></i></a>
                                <div class="t-h-dropdown-menu">
                                    @foreach(DB::table('currencies')->get() as $currency)
                                        <a class="{{ Session::get('currency') == $currency->id ? 'active' : ($currency->is_default == 1 && !Session::has('currency') ? 'active' : '') }}"
                                            href="{{ route('front.currency.setup', $currency->id) }}"><i
                                                class="icon-chevron-right pr-2"></i>{{ $currency->name }}</a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="login-register ">
                                @if(!Auth::user())
                                    <a class="track-order-link mr-0" href="{{ route('user.login') }}">
                                        {{ __('Login') }}

                                    </a>
                                @else
                                    <div class="t-h-dropdown">
                                        <div class="main-link">
                                            <i class="icon-user pr-2"></i> <span
                                                class="text-label">{{ Auth::user()->first_name }}</span>
                                        </div>
                                        <div class="t-h-dropdown-menu">
                                            <a href="{{ route('user.dashboard') }}"><i
                                                    class="icon-chevron-right pr-2"></i>{{ __('Dashboard') }}</a>
                                            <a href="{{ route('user.logout') }}"><i
                                                    class="icon-chevron-right pr-2"></i>{{ __('Logout') }}</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Topbar-->
        <div class="topbar">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between">
                            <!-- Logo-->
                            <div class="site-branding"><a class="site-logo align-self-center"
                                    href="{{ route('front.index') }}"><img
                                        src="{{ url('/core/public/storage/images/AfricanCarat-Logo/1.png') }}"
                                        alt="{{ $setting->title }}"></a></div>
                            <!-- Search / Categories-->
                            <div class="search-box-wrap d-none d-lg-block d-flex">
                                <div class="search-box-inner align-self-center">
                                    <div class="search-box d-flex">






                                        <form class="input-group" id="header_search_form"
                                            action="{{ route('front.catalog') }}" method="get">
                                            <input type="hidden" name="category" value=""
                                                id="search__category">
                                            <span class="input-group-btn">
                                                <button type="submit"><i class="icon-search"></i></button>
                                            </span>
                                            <input class="form-control" type="text"
                                                data-target="{{ route('front.search.suggest') }}"
                                                id="__product__search" name="search"
                                                placeholder="{{ __('Search by product name') }}">
                                            <div class="serch-result d-none">
                                                
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <span class="d-block d-lg-none close-m-serch"><i class="icon-x"></i></span>
                            </div>
                            <!-- Toolbar-->
                            <div class="toolbar d-flex">

                                <div class="toolbar-item close-m-serch visible-on-mobile"><a href="#">
                                        <div>
                                            <i class="icon-search"></i>
                                        </div>
                                    </a>
                                </div>
                                <div class="toolbar-item visible-on-mobile mobile-menu-toggle"><a href="#">
                                        <div><i class="icon-menu"></i><span
                                                class="text-label">{{ __('Menu') }}</span></div>
                                    </a>
                                </div>

                                <div class="toolbar-item hidden-on-mobile"><a
                                        href="{{ route('diamonds.compare.index') }}">
                                        <div><span class="compare-icon"><i class="icon-repeat"></i><span
                                                    class="count-label compare_count">{{ Session::has('diamond_compare') ? count(Session::get('diamond_compare')) : '0' }}</span></span><span
                                                class="text-label">{{ __('Compare') }}</span></div>
                                    </a>
                                </div>
                                @if(Auth::check())
                                    <div class="toolbar-item hidden-on-mobile"><a
                                            href="{{ route('user.wishlist.index') }}">
                                            <div><span class="compare-icon"><i class="icon-heart"></i><span
                                                        class="count-label wishlist_count">{{ Auth::user()->wishlists->count() }}</span></span><span
                                                    class="text-label">{{ __('Wishlist') }}</span></div>
                                        </a>
                                    </div>
                                @else
                                    <div class="toolbar-item hidden-on-mobile"><a
                                            href="{{ route('user.wishlist.index') }}">
                                            <div><span class="compare-icon"><i class="icon-heart"></i></span><span
                                                    class="text-label">{{ __('Wishlist') }}</span></div>
                                        </a>
                                    </div>
                                @endif

                                
                                <div class="toolbar-item d-none d-lg-block toolbar-item--meta">
                                    <a href="{{ route('front.order.track') }}">
                                        <div>
                                            <span class="compare-icon"><i class="icon-map-pin"></i></span>
                                            <span class="text-label">{{ __('Track Order') }}</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="toolbar-item d-none d-lg-block toolbar-item--meta">
                                    <a href="#" role="button"
                                        onclick="event.preventDefault();"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                        <div>
                                            <span class="compare-icon"><i class="icon-globe"></i></span>
                                            <span class="text-label">{{ __('Currency') }}</span>
                                        </div>
                                    </a>

                                    <div class="toolbar-dropdown currency-toolbar-dropdown" role="menu">
                                        @foreach(DB::table('currencies')->get() as $currency)
                                            <li>
                                                <a role="menuitem"
                                                    class="{{ Session::get('currency') == $currency->id ? 'active' : ($currency->is_default == 1 && !Session::has('currency') ? 'active' : '') }}"
                                                    href="{{ route('front.currency.setup', $currency->id) }}"><i
                                                        class="icon-chevron-right pr-2"></i>{{ $currency->name }}</a>
                                            </li>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="toolbar-item d-none d-lg-block toolbar-item--meta">
                                    @if(!Auth::user())
                                        <a href="{{ route('user.login') }}">
                                            <div>
                                                <span class="compare-icon"><i class="icon-log-in"></i></span>
                                                <span class="text-label">{{ __('Login') }}</span>
                                            </div>
                                        </a>
                                    @else
                                        <a href="#" role="button" onclick="event.preventDefault();" aria-haspopup="true">
                                            <div>
                                                <span class="compare-icon"><i class="icon-user"></i></span>
                                                <span class="text-label">{{ Auth::user()->first_name }}</span>
                                            </div>
                                        </a>
                                        <div class="toolbar-dropdown account-toolbar-dropdown" role="menu">
                                            <li>
                                                <a role="menuitem"
                                                    href="{{ route('user.dashboard') }}"><i
                                                        class="icon-chevron-right pr-2"></i>{{ __('Dashboard') }}</a>
                                            </li>
                                            <li>
                                                <a role="menuitem"
                                                    href="{{ route('user.logout') }}"><i
                                                        class="icon-chevron-right pr-2"></i>{{ __('Logout') }}</a>
                                            </li>
                                        </div>
                                    @endif
                                </div>













                                                            <div class="toolbar-item"><a href="{{ route('front.cart') }}"
                                                                    class="toolbar-cart-link"
                                                                    title="{{ __('View cart') }}">
                                                                    <div><span class="cart-icon"><i class="icon-shopping-cart"></i><span
                                                                                class="count-label cart_count">{{ Session::has('cart') ? count(Session::get('cart')) : '0' }}

                                                                            </span></span><span class="text-label">{{ __('Cart') }}</span>
                                                                    </div>
                                                                </a>
                                                                <span class="d-none" id="header_cart_load"
                                                                    data-target="{{ route('front.header.cart') }}"></span>
                                                                <span class="d-none cart_view_header" aria-hidden="true"></span>
                                                            </div>
                                                        </div>

                                                        <!-- Mobile Menu-->
                                                        <div class="mobile-menu">
                                                            <!-- Slideable (Mobile) Menu-->
                                                            <div class="mm-heading-area">
                                                                <h4>{{ __('Navigation') }}</h4>
                                                                <div class="toolbar-item visible-on-mobile mobile-menu-toggle mm-t-two">
                                                                    <a href="#">
                                                                        <div> <i class="icon-x"></i></div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <ul class="nav nav-tabs" role="tablist">
                                                                <li class="nav-item" role="presentation99">
                                                                    <span class="active" id="mmenu-tab" data-bs-toggle="tab"
                                                                        data-bs-target="#mmenu" role="tab" aria-controls="mmenu"
                                                                        aria-selected="true">{{ __('Menu') }}</span>
                                                                </li>
                                                                <li class="nav-item" role="presentation99">
                                                                    <span class="" id="mcat-tab" data-bs-toggle="tab"
                                                                        data-bs-target="#mcat" role="tab" aria-controls="mcat"
                                                                        aria-selected="false">{{ __('Category') }}</span>
                                                                </li>

                                                            </ul>
                                                            <div class="tab-content p-0">
                                                                <div class="tab-pane fade show active" id="mmenu" role="tabpanel"
                                                                    aria-labelledby="mmenu-tab">
                                                                    <nav class="slideable-menu">
                                                                        <ul>
                                                                            <li class="{{ request()->routeIs('front.index') ? 'active' : '' }}"><a
                                                                                    href="{{ route('front.index') }}"><i
                                                                                        class="icon-chevron-right"></i>{{ __('Home') }}</a>
                                                                            </li>
                                                                            @if($setting->is_shop == 1)
                                                                                <li
                                                                                    class="{{ request()->routeIs('front.catalog*') ? 'active' : '' }}">
                                                                                    <a href="{{ route('front.catalog') }}"><i
                                                                                            class="icon-chevron-right"></i>{{ __('Shop') }}</a>
                                                                                </li>
                                                                            @endif
                                                                            @if($setting->is_campaign == 1)
                                                                                <li
                                                                                    class="{{ request()->routeIs('front.campaign') ? 'active' : '' }}">
                                                                                    <a href="{{ route('front.campaign') }}"><i
                                                                                            class="icon-chevron-right"></i>{{ __('Campaign') }}</a>
                                                                                </li>
                                                                            @endif
                                                                            @if($setting->is_brands == 1)
                                                                                <li
                                                                                    class="{{ request()->routeIs('front.brand') ? 'active' : '' }}">
                                                                                    <a href="{{ route('front.brand') }}"><i
                                                                                            class="icon-chevron-right"></i>{{ __('Brand') }}</a>
                                                                                </li>
                                                                            @endif

                                                                            @if($setting->is_blog == 1)
                                                                                <li
                                                                                    class="{{ request()->routeIs('front.blog*') ? 'active' : '' }}">
                                                                                    <a href="{{ route('front.blog') }}"><i
                                                                                            class="icon-chevron-right"></i>{{ __('Blog') }}</a>
                                                                                </li>
                                                                            @endif
                                                                            <li class="t-h-dropdown">
                                                                                <a class="" href="#"><i
                                                                                        class="icon-chevron-right"></i>{{ __('Pages') }} <i
                                                                                        class="icon-chevron-down"></i></a>
                                                                                <div class="t-h-dropdown-menu">
                                                                                    @if($setting->is_faq == 1)
                                                                                        <a class="{{ request()->routeIs('front.faq*') ? 'active' : '' }}"
                                                                                            href="{{ route('front.faq') }}"><i
                                                                                                class="icon-chevron-right pr-2"></i>{{ __('Faq') }}</a>
                                                                                    @endif
                                                                                    @foreach(DB::table('pages')->wherePos(0)->orwhere('pos', 2)->get() as $page)
                                                                                        <a class="{{ request()->url() == route('front.page', $page->slug) ? 'active' : '' }} "
                                                                                            href="{{ route('front.page', $page->slug) }}"><i
                                                                                                class="icon-chevron-right pr-2"></i>{{ $page->title }}</a>
                                                                                    @endforeach
                                                                                </div>
                                                                            </li>

                                                                            @if($setting->is_contact == 1)
                                                                                <li
                                                                                    class="{{ request()->routeIs('front.contact') ? 'active' : '' }}">
                                                                                    <a href="{{ route('front.contact') }}"><i
                                                                                            class="icon-chevron-right"></i>{{ __('Contact') }}</a>
                                                                                </li>
                                                                            @endif
                                                                        </ul>
                                                                    </nav>
                                                                </div>
                                                                <div class="tab-pane fade" id="mcat" role="tabpanel"
                                                                    aria-labelledby="mcat-tab">
                                                                    <nav class="slideable-menu">
                                                                        @include('includes.mobile-category')

                                                                    </nav>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Navbar-->
                                    <div class="navbar">
                                        <div class="container">
                                            <div class="row g-3 w-100 align-items-center">
                                                
                    @if($setting->is_show_category == 1)
                        <div class="col-12 col-lg-auto pr-lg-2">
                            @include('includes.categories')
                        </div>
                    @endif
                    <div class="col-12 col-lg d-flex justify-content-between min-w-0">
                        <div class="nav-inner">
                            @include('master.inc.site-menu')
                        </div>
                        @php
                            $free_shipping = DB::table('shipping_services')
                                ->whereStatus(1)
                                ->whereIsCondition(1)
                                ->first();
                        @endphp

                    </div>
                </div>
            </div>
        </div>

    </header>
