<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $configured = config('app.url');
        $path = '';

        if (is_string($configured) && $configured !== '') {
            $path = rtrim((string) parse_url($configured, PHP_URL_PATH), '/');
        }

        // Use the current host so assets work via hostname (not only localhost).
        if (! app()->runningInConsole()) {
            URL::forceRootUrl(rtrim(request()->getSchemeAndHttpHost().$path, '/'));

            return;
        }

        if (is_string($configured) && $configured !== '') {
            URL::forceRootUrl(rtrim($configured, '/'));
        }
    }
}
