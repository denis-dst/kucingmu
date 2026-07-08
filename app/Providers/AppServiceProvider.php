<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // Share settings globally to all views if the table exists
        if (Schema::hasTable('app_settings')) {
            $settings = \App\Models\AppSetting::pluck('value', 'key')->all();
            view()->share('app_settings', $settings);
        }
    }
}
