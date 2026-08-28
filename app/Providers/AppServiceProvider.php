<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS in production or if accessed via HTTPS proxy / Cloudflare
        if (config('app.env') === 'production' || request()->header('X-Forwarded-Proto') === 'https' || request()->server('HTTPS') === 'on') {
            URL::forceScheme('https');
        }

        // Share settings globally to all views if the table exists
        if (Schema::hasTable('app_settings')) {
            $settings = \App\Models\AppSetting::pluck('value', 'key')->all();
            view()->share('app_settings', $settings);
        }
    }
}
