<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    @php
        $siteFavicon = $setting->favicon;
        foreach (['AfricanCarat-Logo/favicon.png', 'AfricanCarat-Logo/1.png', 'African carat/1.png'] as $faviconCandidate) {
            if (is_file(public_path('storage/images/' . $faviconCandidate))) {
                $siteFavicon = $faviconCandidate;
                break;
            }
        }
        $siteFaviconUrl = url('/core/public/storage/images/' . $siteFavicon);
    @endphp
    <link rel="manifest" href="{{ url('/core/public/manifest.json') }}">
    <meta name="pwa-sw-url" content="{{ url('/core/public/sw.js') }}">
    <meta name="theme-color" content="#0A0A0A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="apple-touch-icon" href="{{ $siteFaviconUrl }}">
@if (url()->current() == route('front.index'))
        <title>@yield('hometitle')</title>
    @else
        <title>{{ $setting->title }} -@yield('title')</title>
    @endif

    <!-- SEO Meta Tags-->
    @if (url()->current() == route('front.index'))
        <meta name="author" content="GeniusDevs">
        <meta name="distribution" content="web">
        <meta name="description" content="{{ $setting->meta_description }}">
        <meta name="keywords" content="{{ $setting->meta_keywords }}">
        <meta name="image" content="{{ url('/core/public/storage/images/' . $setting->meta_image) }}">
        <meta property="og:title" content="{{ $setting->title }}">
        <meta property="og:description" content="{{ $setting->meta_description }}">
        <meta property="og:image" content="{{ url('/core/public/storage/images/' . $setting->meta_image) }}">
        <meta property="og:image:secure_url"
            content="{{ url('/core/public/storage/images/' . $setting->meta_image) }}" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="627" />
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ $setting->title }}">
        <meta property="og:type" content="website">
    @else
        @yield('meta')
    @endif

    <!-- Mobile Specific Meta Tag-->
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <!-- Favicon Icons (African Carat file when present, else admin upload) -->
    <link rel="icon" type="image/png" href="{{ $siteFaviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $siteFaviconUrl }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ $siteFaviconUrl }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $siteFaviconUrl }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ $siteFaviconUrl }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- Vendor Styles including: Bootstrap, Font Icons, Plugins, etc.-->
    <link rel="stylesheet" media="screen" href="{{ asset('assets/front/css/plugins.min.css') }}">

    @yield('styleplugins')

    <link id="mainStyles" rel="stylesheet" media="screen" href="{{ asset('assets/front/css/styles.min.css') }}">

    <link id="mainStyles" rel="stylesheet" media="screen" href="{{ asset('assets/front/css/responsive.css') }}">
    <!-- Color css -->
    <link
        href="{{ asset('assets/front/css/color.php?primary_color=') . str_replace('#', '', $setting->primary_color) }}"
        rel="stylesheet">

    <!-- Modernizr-->
    <script src="{{ asset('assets/front/js/modernizr.min.js') }}"></script>

    @if (DB::table('languages')->where('is_default', 1)->first()->rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/front/css/rtl.css') }}">
    @endif

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/front/css/hw-header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/fonts-williams-caslon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/css/mobile-responsive.css') }}">

    <style>
        {{ $setting->custom_css }}
    </style>
    {{-- Google AdSense Start --}}
    @if ($setting->is_google_adsense == '1')
        {!! html_entity_decode($setting->google_adsense) !!}
    @endif
    {{-- Google AdSense End --}}

    {{-- Google AnalyTics Start --}}
    @if ($setting->is_google_analytics == '1')
        {!! html_entity_decode($setting->google_analytics) !!}
    @endif
    {{-- Google AnalyTics End --}}

    {{-- Facebook pixel  Start --}}
    @if ($setting->is_facebook_pixel == '1')
        {!! html_entity_decode($setting->facebook_pixel) !!}
    @endif
    {{-- Facebook pixel End --}}
    <script defer src='https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js'></script>
</head>
<!-- Body-->

<body style="padding-top: 0;"
    data-cache-pdp="{{ request()->routeIs('front.product') ? '1' : '0' }}"
    class="
 front-has-mobile-tabs
@if ($setting->theme == 'theme1') body_theme1
@elseif($setting->theme == 'theme2')
body_theme2
@elseif($setting->theme == 'theme3')
body_theme3
@elseif($setting->theme == 'theme4')
body_theme4 @endif
">
    @if ($setting->is_loader == 1)
        <!-- Preloader Start -->
        @if ($setting->is_loader == 1)
            <div id="preloader">
                <img src="{{ url('/core/public/storage/images/' . $setting->loader) }}" alt="{{ __('Loading...') }}">
            </div>
        @endif

        <!-- Preloader endif -->
    @endif

    @include('master.inc.hw-header')

    <!-- Page Content-->
    @yield('content')

    <!--    announcement banner section start   -->
    <a class="announcement-banner" href="#announcement-modal"></a>
    <div id="announcement-modal" class="mfp-hide white-popup">
        @if ($setting->announcement_type == 'newletter')
            <div class="announcement-with-content">
                <div class="left-area">
                    <img src="{{ url('/core/public/storage/images/' . $setting->announcement) }}" alt="">
                </div>
                <div class="right-area">
                    <h3 class="">{{ $setting->announcement_title }}</h3>
                    <p>{{ $setting->announcement_details }}</p>
                    <form class="subscriber-form" action="{{ route('front.subscriber.submit') }}" method="post">
                        @csrf
                        <div class="input-group">
                            <input class="form-control" type="email" name="email"
                                placeholder="{{ __('Your e-mail') }}">
                            <span class="input-group-addon"><i class="icon-mail"></i></span>
                        </div>
                        <div aria-hidden="true">
                            <input type="hidden" name="b_c7103e2c981361a6639545bd5_1194bb7544" tabindex="-1">
                        </div>

                        <button class="btn btn-luxury btn-block mt-2" type="submit">
                            <span>{{ __('Subscribe') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ $setting->announcement_link }}">
                <img src="{{ url('/core/public/storage/images/' . $setting->announcement) }}" alt="">
            </a>
        @endif


    </div>
    <!--    announcement banner section end   -->

    <!-- Site Footer-->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <!-- Contact Info-->
                    <section class="widget widget-light-skin">
                        <h3 class="widget-title">{{ __('GET IN TOUCH') }}</h3>
                        <p class="mb-1"><strong>{{ __('Address') }}: </strong> {{ $setting->footer_address }}</p>
                        <p class="mb-1"><strong>{{ __('Phone') }}: </strong> {{ $setting->footer_phone }}</p>
                        <p class="mb-1"><strong>{{ __('Email') }}: </strong> {{ $setting->footer_email }}</p>
                        <ul class="list-unstyled text-sm">
                            <li><span class=""><strong>{{ $setting->working_days_from_to }}:
                                    </strong></span>{{ $setting->friday_start }} - {{ $setting->friday_end }}</li>
                        </ul>
                        @php
                            $links = json_decode($setting->social_link, true)['links'];
                            $icons = json_decode($setting->social_link, true)['icons'];

                        @endphp
                        <div class="footer-social-links">
                            @foreach ($links as $link_key => $link)
                                <a href="{{ $link }}"><span><i
                                            class="{{ $icons[$link_key] }}"></i></span></a>
                            @endforeach
                        </div>
                    </section>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <!-- Customer Info-->
                    <div class="widget widget-links widget-light-skin">
                        <h3 class="widget-title">{{ __('USEFUL LINKS') }}</h3>
                        <ul>
                            @if ($setting->is_faq == 1)
                                <li>
                                    <a class="" href="{{ route('front.faq') }}">{{ __('Faq') }}</a>
                                </li>
                            @endif
                            @foreach (DB::table('pages')->wherePos(2)->orwhere('pos', 1)->get() as $page)
                                <li><a href="{{ route('front.page', $page->slug) }}">{{ $page->title }}</a></li>
                            @endforeach

                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Subscription-->
                    <section class="widget">
                        <h3 class="widget-title">{{ __('NEWSLETTER') }}</h3>
                        <form class="row subscriber-form" action="{{ route('front.subscriber.submit') }}"
                            method="post">
                            @csrf
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input class="form-control" type="email" name="email"
                                        placeholder="{{ __('Your e-mail') }}">
                                    <span class="input-group-addon"><i class="icon-mail"></i></span>
                                </div>
                                <div aria-hidden="true">
                                    <input type="hidden" name="b_c7103e2c981361a6639545bd5_1194bb7544"
                                        tabindex="-1">
                                </div>

                            </div>
                            <div class="col-sm-12">
                                <button class="btn btn-luxury btn-block mt-2" type="submit">
                                    <span>{{ __('Subscribe') }}</span>
                                </button>
                            </div>
                            <div class="col-lg-12">
                                <p class="text-sm opacity-80 pt-2">
                                    {{ __('Subscribe to our Newsletter to receive early discount offers, latest news, sales and promo information.') }}
                                </p>
                            </div>
                        </form>
                        <div class="pt-3"><img class="d-block gateway_image"
                                src="{{ $setting->footer_gateway_img ? url('/core/public/storage/images/' . $setting->footer_gateway_img) : asset('system/resources/assets/images/placeholder.png') }}">
                        </div>
                    </section>
                </div>
            </div>
            <div class="luxury-footer__middle container">
                <div class="luxury-footer__links">
                    <a href="/education/guides/engagement-ring-guide">Engagement Rings Guide</a>
                    <a href="/education/guides/wedding-bands-guide">Wedding Rings Guide</a>
                    <a href="/education/guides/metal-types">Metals</a>
                    <a href="{{ route('education.index') }}">Diamonds Guide</a>
                    <a href="{{ route('education.compliance') }}">{{ __('Trust & compliance') }}</a>
                    <a href="/education/guides/ring-settings">Ring Settings</a>
                    <a href="/about-us">About</a>
                </div>
            </div>
            <!-- Copyright-->
            <p class="footer-copyright"> {{ $setting->copy_right }}</p>
        </div>
    </footer>

    @include('components.mobile-tab-bar')

    <div id="luxuryPwaBanner" class="pwa-install-banner" role="status" aria-live="polite">
        <span>{{ __('Install our app for faster repeat visits.') }}</span>
        <button type="button" class="pwa-install-banner__btn" id="luxuryPwaInstallBtn">{{ __('Install') }}</button>
        <button type="button" class="btn btn-link btn-sm text-white ms-2 p-0 align-self-center"
            onclick="try{sessionStorage.setItem('luxuryPwaDismiss','1')}catch(_){};this.closest('#luxuryPwaBanner').classList.remove('is-visible')" style="text-decoration:underline;font-size:12px;">
            {{ __('Dismiss') }}
        </button>
    </div>

    <!-- Back To Top Button-->
    <a class="scroll-to-top-btn" href="#">
        <i class="icon-chevron-up"></i>
    </a>
    <!-- Backdrop-->
    <div class="site-backdrop"></div>

    <!-- Cookie alert dialog  -->
    @if ($setting->is_cookie == 1)
        @include('cookie-consent::index')
    @endif
    <!-- Cookie alert dialog  -->


    @php
        $mainbs = [];
        $mainbs['is_announcement'] = $setting->is_announcement;
        $mainbs['announcement_delay'] = $setting->announcement_delay;
        $mainbs['overlay'] = $setting->overlay;
        $mainbs = json_encode($mainbs);
    @endphp

    <script>
        var mainbs = {!! $mainbs !!};
        var decimal_separator = '{!! $setting->decimal_separator !!}';
        var thousand_separator = '{!! $setting->thousand_separator !!}';
    </script>

    <script>
        let language = {
            Days: '{{ __('Days') }}',
            Hrs: '{{ __('Hrs') }}',
            Min: '{{ __('Min') }}',
            Sec: '{{ __('Sec') }}',
        }
    </script>



    <!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
    <script type="text/javascript" src="{{ asset('assets/front/js/plugins.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/back/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('assets/front/js/scripts.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/lazy.plugin.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/front/js/myscript.js') }}"></script>

    @yield('script')
    @yield('scripts')

    @if ($setting->is_facebook_messenger == '1')
        <!-- Messenger Chat Plugin Code -->
        <div id="fb-root"></div>

        <!-- Your Chat Plugin code -->
        <div id="fb-customer-chat" class="fb-customerchat">
        </div>

        <script>
            var chatbox = document.getElementById('fb-customer-chat');
            chatbox.setAttribute("page_id", "{{ $setting->facebook_messenger }}");
            chatbox.setAttribute("attribution", "biz_inbox");
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v11.0'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
    @endif



    <script type="text/javascript">
        let mainurl = '{{ route('front.index') }}';

        let view_extra_index = 0;
        // Notifications
        function SuccessNotification(title) {
            $.notify({
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-check-circle'
            }, {
                element: 'body',
                position: null,
                type: "success",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }

        function DangerNotification(title) {
            $.notify({
                // options
                title: ` <strong>${title}</strong>`,
                message: '',
                icon: 'fas fa-exclamation-triangle'
            }, {
                // settings
                element: 'body',
                position: null,
                type: "danger",
                allow_dismiss: true,
                newest_on_top: false,
                showProgressbar: false,
                placement: {
                    from: "top",
                    align: "right"
                },
                offset: 20,
                spacing: 10,
                z_index: 1031,
                delay: 5000,
                timer: 1000,
                url_target: '_blank',
                mouse_over: null,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },
                onShow: null,
                onShown: null,
                onClose: null,
                onClosed: null,
                icon_type: 'class'
            });
        }
        // Notifications Ends
    </script>

    @if (Session::has('error'))
        <script>
            $(document).ready(function() {
                DangerNotification('{{ Session::get('error') }}')
            })
        </script>
    @endif
    @if (Session::has('success'))
        <script>
            $(document).ready(function() {
                SuccessNotification('{{ Session::get('success') }}');
            })
        </script>
    @endif

</body>

</html>
