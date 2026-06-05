<?php

namespace App\Providers;

use Illuminate\{
    Support\ServiceProvider,
    Support\Facades\DB
};
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerVitePublicUrlPrefix();

        Paginator::useBootstrap();
        view()->composer('*', function ($settings) {
            $setting = DB::table('settings')->find(1);
            if ($setting) {
                $setting->title = 'African Carat';
                $setting->home_page_title = 'African Carat';
            }
            $settings->with('setting', $setting);
            $settings->with('extra_settings', DB::table('extra_settings')->find(1));
            $settings->with('menus', DB::table('menus')->find(1));

            if (!session()->has('popup')) {
                view()->share('visit', 1);
            }
            session()->put('popup', 1);
        });
    }

    public function register()
    {
    }

    /**
     * Vite builds to core/public/build, but HTML uses paths like /build/... .
     * When the HTTP docroot is the parent folder (index.php next to assets/core),
     * /build/… is wrong unless there is a root symlink — use /core/public/build/… instead.
     *
     * .env: VITE_ASSET_PREFIX=core/public (force), empty + non-local APP_URL → same default,
     * VITE_ASSET_PREFIX=false disables (e.g. you symlink monteluca.com/build → core/public/build).
     */
    protected function registerVitePublicUrlPrefix(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $raw = env('VITE_ASSET_PREFIX');

        if (in_array(strtolower((string) $raw), ['0', 'false', 'off', 'none'], true)) {
            return;
        }

        $configured = trim((string) ($raw ?? ''), '/');

        $prefix = $configured !== '' ? $configured : '';

        if ($prefix === '') {
            $host = strtolower((string) parse_url(config('app.url'), PHP_URL_HOST));
            $isLocalDoc = in_array($host, ['127.0.0.1', 'localhost', '::1'], true);
            $parentBootstrap = is_file(dirname(base_path()).\DIRECTORY_SEPARATOR.'index.php');
            $fallbackPrefix = trim((string) env('VITE_ASSET_FALLBACK_PREFIX', 'core/public'), '/');
            if (! $isLocalDoc && $parentBootstrap && $fallbackPrefix !== '') {
                $prefix = $fallbackPrefix;
            }
        }

        if ($prefix === '') {
            return;
        }

        Vite::createAssetPathsUsing(function ($path, $secure = null) use ($prefix) {
            return asset($prefix.'/'.$path, $secure);
        });
    }
}