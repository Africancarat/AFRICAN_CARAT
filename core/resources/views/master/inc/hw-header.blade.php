{{-- Harry Winston–style header: dark two-row bar, mega menus on hover --}}
@php
    // 2.png = white wordmark (visible on dark header); 1.png is a dark bar and does not show on #000014
    $hwLogoPath = '/core/public/storage/images/AfricanCarat-Logo/2.png';
    $hwLogoMain = url($hwLogoPath);
    if (!is_file(public_path('storage/images/AfricanCarat-Logo/2.png'))) {
        $hwLogoMain = $setting->logo
            ? url('/core/public/storage/images/' . $setting->logo)
            : url('/core/public/storage/images/placeholder.png');
    }
@endphp

<header class="site-header site-header--hw navbar-sticky">
    <div class="hw-header__top">
        <div class="container-fluid hw-header__top-inner">
            <div class="hw-header__tools hw-header__tools--left">
                <button type="button"
                    class="hw-header__icon-btn hw-header__menu-btn mobile-menu-toggle"
                    aria-label="{{ __('Menu') }}">
                    <i class="icon-menu" aria-hidden="true"></i>
                </button>
                @if ($setting->is_contact == 1)
                    <a href="{{ route('front.contact') }}" class="hw-header__icon-btn d-none d-lg-inline-flex"
                        aria-label="{{ __('Contact') }}" title="{{ __('Contact') }}">
                        <i class="icon-phone" aria-hidden="true"></i>
                    </a>
                @endif
                <button type="button" class="hw-header__icon-btn hw-header__search-toggle"
                    aria-label="{{ __('Search') }}" aria-expanded="false" aria-controls="hw-header-search">
                    <i class="icon-search" aria-hidden="true"></i>
                </button>
            </div>

            <div class="hw-header__brand">
                <a class="hw-header__logo-link" href="{{ route('front.index') }}">
                    <span class="hw-header__logo-frame">
                        <img class="hw-header__logo-main" src="{{ $hwLogoMain }}"
                            alt="{{ $setting->title }}" width="360" height="56" decoding="async">
                    </span>
                </a>
            </div>

            <div class="hw-header__tools hw-header__tools--right">
                @if ($setting->is_contact == 1)
                    <a href="{{ route('front.contact') }}" class="hw-header__icon-btn"
                        aria-label="{{ __('Store locator') }}" title="{{ __('Store locator') }}">
                        <i class="icon-map-pin" aria-hidden="true"></i>
                    </a>
                @endif
                <a href="{{ route('user.wishlist.index') }}"
                    class="hw-header__icon-btn hw-header__icon-btn--compact-hide d-none d-lg-inline-flex"
                    aria-label="{{ __('Wishlist') }}">
                    <i class="icon-heart" aria-hidden="true"></i>
                </a>

                <div class="hw-header__dropdown d-none d-lg-inline-flex">
                    <button type="button" class="hw-header__icon-btn" aria-label="{{ __('Currency') }}"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="icon-globe" aria-hidden="true"></i>
                    </button>
                    <div class="hw-header__dropdown-menu" role="menu">
                        @foreach (DB::table('currencies')->get() as $currency)
                            <a role="menuitem"
                                class="{{ Session::get('currency') == $currency->id ? 'active' : ($currency->is_default == 1 && !Session::has('currency') ? 'active' : '') }}"
                                href="{{ route('front.currency.setup', $currency->id) }}">
                                <i class="icon-chevron-right pr-2"></i>{{ $currency->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="hw-header__dropdown d-none d-lg-inline-flex">
                    @guest
                        <button type="button" class="hw-header__icon-btn" aria-label="{{ __('Account') }}"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="icon-user" aria-hidden="true"></i>
                        </button>
                        <div class="hw-header__dropdown-menu" role="menu">
                            <a role="menuitem" href="{{ route('user.login') }}">
                                <i class="icon-chevron-right pr-2"></i>{{ __('Login') }}
                            </a>
                            <a role="menuitem" href="{{ route('user.register') }}">
                                <i class="icon-chevron-right pr-2"></i>{{ __('Register') }}
                            </a>
                        </div>
                    @else
                        <button type="button" class="hw-header__icon-btn" aria-label="{{ Auth::user()->first_name }}"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="icon-user" aria-hidden="true"></i>
                        </button>
                        <div class="hw-header__dropdown-menu" role="menu">
                            <a role="menuitem" href="{{ route('user.dashboard') }}">
                                <i class="icon-chevron-right pr-2"></i>{{ __('Dashboard') }}
                            </a>
                            <a role="menuitem" href="{{ route('user.logout') }}">
                                <i class="icon-chevron-right pr-2"></i>{{ __('Logout') }}
                            </a>
                        </div>
                    @endguest
                </div>

                <a href="{{ route('front.cart') }}"
                    class="hw-header__icon-btn hw-header__icon-btn--cart hw-header__icon-btn--compact-hide"
                    aria-label="{{ __('Cart') }}" title="{{ __('View cart') }}">
                    <i class="icon-shopping-cart" aria-hidden="true"></i>
                    <span class="hw-header__cart-count cart_count">{{ Session::has('cart') ? count(Session::get('cart')) : '0' }}</span>
                </a>
                <span class="d-none" id="header_cart_load"
                    data-target="{{ route('front.header.cart') }}"></span>
                <span class="d-none cart_view_header" aria-hidden="true"></span>
            </div>
        </div>
    </div>

    <div id="hw-header-search" class="hw-header__search-panel" hidden>
        <div class="container-fluid">
            <form class="hw-header__search-form" id="header_search_form" action="{{ route('front.catalog') }}"
                method="get" role="search">
                <input type="hidden" name="category" value="" id="search__category">
                <input class="hw-header__search-input form-control" type="search"
                    data-target="{{ route('front.search.suggest') }}" id="__product__search" name="search"
                    placeholder="{{ __('Search by product name') }}" autocomplete="off">
                <button type="submit" class="hw-header__search-submit" aria-label="{{ __('Search') }}">
                    <i class="icon-search" aria-hidden="true"></i>
                </button>
                <button type="button" class="hw-header__search-close hw-header__search-toggle"
                    aria-label="{{ __('Close search') }}">
                    <i class="icon-x" aria-hidden="true"></i>
                </button>
                <div class="serch-result d-none"></div>
            </form>
        </div>
    </div>

    <nav class="hw-header__nav d-none d-lg-block" aria-label="{{ __('Main navigation') }}">
        <div class="container-fluid hw-header__nav-wrap">
            <div class="hw-header__nav-inner nav-inner">
                @include('master.inc.site-menu')
            </div>
        </div>
    </nav>

    <div class="mobile-menu">
        <div class="mm-heading-area">
            <h4>{{ __('Navigation') }}</h4>
            <div class="toolbar-item visible-on-mobile mobile-menu-toggle mm-t-two">
                <a href="#"><div><i class="icon-x"></i></div></a>
            </div>
        </div>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <span class="active" id="mmenu-tab" data-bs-toggle="tab" data-bs-target="#mmenu" role="tab"
                    aria-controls="mmenu" aria-selected="true">{{ __('Menu') }}</span>
            </li>
            <li class="nav-item" role="presentation">
                <span id="mcat-tab" data-bs-toggle="tab" data-bs-target="#mcat" role="tab"
                    aria-controls="mcat" aria-selected="false">{{ __('Category') }}</span>
            </li>
        </ul>
        <div class="tab-content p-0">
            <div class="tab-pane fade show active" id="mmenu" role="tabpanel" aria-labelledby="mmenu-tab">
                <nav class="hw-mobile-menu" id="hw-mobile-menu" aria-label="{{ __('Main menu') }}">
                    @include('master.inc.site-menu', ['navContext' => 'mobile'])
                    <ul class="hw-mobile-menu__extras list-unstyled">
                        <li><a href="{{ route('front.order.track') }}">{{ __('Track Order') }}</a></li>
                        <li><a href="{{ route('diamonds.compare.index') }}">{{ __('Compare') }}</a></li>
                        @guest
                            <li><a href="{{ route('user.login') }}">{{ __('Login') }}</a></li>
                            <li><a href="{{ route('user.register') }}">{{ __('Register') }}</a></li>
                        @else
                            <li><a href="{{ route('user.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li><a href="{{ route('user.logout') }}">{{ __('Logout') }}</a></li>
                        @endguest
                        @foreach (DB::table('currencies')->get() as $currency)
                            <li>
                                <a class="{{ Session::get('currency') == $currency->id ? 'active' : ($currency->is_default == 1 && !Session::has('currency') ? 'active' : '') }}"
                                    href="{{ route('front.currency.setup', $currency->id) }}">{{ $currency->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
            <div class="tab-pane fade" id="mcat" role="tabpanel" aria-labelledby="mcat-tab">
                <nav class="slideable-menu">
                    @include('includes.mobile-category')
                </nav>
            </div>
        </div>
    </div>
</header>
