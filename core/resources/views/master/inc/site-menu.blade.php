@php
    $navContext = $navContext ?? 'desktop';
    $isMobileNav = $navContext === 'mobile';
    $links = isset($menus) && $menus ? json_decode($menus->menus, true) : [];
    $links = is_array($links) ? $links : [];
@endphp

<nav class="site-menu luxury-nav-wrap hw-site-menu{{ $isMobileNav ? ' hw-site-menu--drawer' : '' }}"
    @if ($isMobileNav) aria-label="{{ __('Mobile navigation') }}" @endif>
    <ul class="luxury-nav hw-nav{{ $isMobileNav ? ' hw-nav--drawer' : '' }} list-unstyled mb-0">
        @foreach ($links as $link)
            @php
                $href = Helper::getHref($link);
                $megaKey = \App\Support\LuxuryMegaMenu::keyForMenuLink($link);
                $finalHref = ($link['href'] ?? null) === null ? $href : $link['href'];
                $linkTarget = $link['target'] ?? '_self';
                $drawerPanelId = $megaKey ? 'hw-drawer-mega-' . $megaKey : null;
                $drawerChildrenId = 'hw-drawer-children-' . md5(($link['text'] ?? '') . $href);
            @endphp

            @if ($megaKey && view()->exists('master.inc.mega-menus.' . $megaKey))
                <li class="t-h-dropdown luxury-nav__item luxury-nav__item--has-mega @if(request()->url() == $finalHref || request()->fullUrlIs($finalHref)) active @endif"
                    data-mega-key="{{ $megaKey }}">
                    @if ($isMobileNav)
                        <div class="hw-drawer__row">
                            <a class="luxury-nav__link hw-drawer__label" href="{{ $finalHref }}"
                                target="{{ $linkTarget }}">{{ $link['text'] }}</a>
                            <button type="button" class="hw-drawer__toggle btn btn-link p-0 border-0"
                                aria-expanded="false" aria-controls="{{ $drawerPanelId }}"
                                aria-label="{{ __('Expand') }} {{ $link['text'] }}">
                                <i class="icon-chevron-down" aria-hidden="true"></i>
                            </button>
                        </div>
                    @else
                        <a class="main-link luxury-nav__link" href="{{ $finalHref }}"
                            target="{{ $linkTarget }}">{{ $link['text'] }}</a>
                    @endif

                    <div @if ($isMobileNav) id="{{ $drawerPanelId }}" @endif
                        class="luxury-mega-panel{{ $isMobileNav ? ' hw-drawer__panel' : '' }}"
                        role="navigation" aria-label="{{ $link['text'] }}">
                        <div class="luxury-mega-panel__scroll">
                            <div class="container luxury-mega-panel__inner py-4 py-lg-5">
                                @include('master.inc.mega-menus.' . $megaKey)
                            </div>
                        </div>
                    </div>
                </li>
            @elseif (!array_key_exists('children', $link))
                <li class="luxury-nav__item @if(request()->url() == ($link['href'] == null ? $href : $link['href'])) active @endif">
                    @if ($isMobileNav)
                        <div class="hw-drawer__row hw-drawer__row--leaf">
                            <a class="luxury-nav__link hw-drawer__label"
                                href="{{ $link['href'] == null ? $href : $link['href'] }}"
                                target="{{ $linkTarget }}">{{ $link['text'] }}</a>
                        </div>
                    @else
                        <a class="luxury-nav__link" href="{{ $link['href'] == null ? $href : $link['href'] }}"
                            target="{{ $linkTarget }}">{{ $link['text'] }}</a>
                    @endif
                </li>
            @else
                <li class="t-h-dropdown luxury-nav__item luxury-nav__item--has-children">
                    @if ($isMobileNav)
                        <div class="hw-drawer__row">
                            <a class="luxury-nav__link hw-drawer__label" href="{{ $href }}"
                                target="{{ $linkTarget }}">{{ $link['text'] }}</a>
                            <button type="button" class="hw-drawer__toggle btn btn-link p-0 border-0"
                                aria-expanded="false" aria-controls="{{ $drawerChildrenId }}"
                                aria-label="{{ __('Expand') }} {{ $link['text'] }}">
                                <i class="icon-chevron-down" aria-hidden="true"></i>
                            </button>
                        </div>
                    @else
                        <a class="main-link luxury-nav__link" href="{{ $href }}" target="{{ $linkTarget }}">{{ $link['text'] }}<i class="icon-chevron-down" aria-hidden="true"></i></a>
                    @endif

                    <div @if ($isMobileNav) id="{{ $drawerChildrenId }}" @endif
                        class="t-h-dropdown-menu luxury-megamenu{{ $isMobileNav ? ' hw-drawer__panel' : '' }}">
                        @foreach ($link['children'] as $level2)
                            @php
                                $l2Href = Helper::getHref($level2);
                            @endphp
                            <a class="hw-drawer__sublink @if ($l2Href == URL::current()) active @endif"
                                href="{{ $l2Href }}" target="{{ $level2['target'] ?? '_self' }}">
                                <i class="icon-chevron-right pr-2" aria-hidden="true"></i>
                                {{ $level2['text'] }}
                            </a>
                        @endforeach
                    </div>
                </li>
            @endif
        @endforeach
    </ul>
</nav>
