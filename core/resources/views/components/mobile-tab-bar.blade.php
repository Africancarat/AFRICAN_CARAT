@if (!isset($hideMobileTabs) || ! $hideMobileTabs)
    <nav class="mobile-tab-bar d-lg-none" aria-label="{{ __('Primary navigation') }}">
        @php
            $isHome = request()->routeIs('front.index');
            $isDiamonds = request()->routeIs('diamonds.*');
            $isEducation = \Illuminate\Support\Str::startsWith((string) request()->path(), 'education');
            $ringsUrl = url('/engagement-rings');
            $isRings = request()->path() === 'engagement-rings' || request()->path() === 'wedding-rings';
            $accountUrl = Auth::check() ? route('user.dashboard') : route('user.login');
            $accountActive = request()->routeIs('user.login')
                || \Illuminate\Support\Str::startsWith((string) request()->path(), 'user/dashboard');
        @endphp
        <a class="mobile-tab-bar__item {{ $isHome ? 'is-active' : '' }}" href="{{ route('front.index') }}">
            <i class="icon-home"></i>
            {{ __('Home') }}
        </a>
        <a class="mobile-tab-bar__item {{ $isDiamonds ? 'is-active' : '' }}" href="{{ route('diamonds.index') }}">
            <i class="icon-aperture"></i>
            {{ __('Diamonds') }}
        </a>
        <a class="mobile-tab-bar__item {{ $isRings ? 'is-active' : '' }}" href="{{ $ringsUrl }}">
            <i class="icon-heart"></i>
            {{ __('Rings') }}
        </a>
        <a class="mobile-tab-bar__item {{ $isEducation ? 'is-active' : '' }}" href="{{ route('education.index') }}">
            <i class="icon-file-text"></i>
            {{ __('Education') }}
        </a>
        <a class="mobile-tab-bar__item {{ $accountActive ? 'is-active' : '' }}" href="{{ $accountUrl }}">
            <i class="icon-user"></i>
            {{ __('Account') }}
        </a>
    </nav>
@endif
