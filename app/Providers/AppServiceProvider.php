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
        if (! app()->runningInConsole()) {
            $basePath = request()->getBasePath();
            $root = rtrim(request()->getSchemeAndHttpHost().$basePath, '/');

            URL::forceRootUrl($root !== '' ? $root : request()->getSchemeAndHttpHost());

            // Keep session/CSRF cookies scoped to the actual IIS virtual directory.
            config([
                'session.path' => $basePath === '' ? '/' : $basePath,
            ]);

            return;
        }

        $configured = config('app.url');

        if (is_string($configured) && $configured !== '') {
            URL::forceRootUrl(rtrim($configured, '/'));
        }
    }
}
