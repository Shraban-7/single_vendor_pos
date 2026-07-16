<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        \Debugbar::disable();

        //Paginator::useBootstrap();
        Paginator::useTailwind();
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'product' => \App\Models\Product::class,
            'category' => \App\Models\Category::class,
            'banner' => \App\Models\Banner::class,
        ]);

        // Share categories with all views
        View::composer('*', function ($view) {
            $settings = cache()->remember('site_settings', 3600, function () {
                return \App\Models\Setting::select('value', 'key')->pluck('value', 'key');
            });

            $siteName = $settings['site_name'] ?? null;

            View::share('siteName', $siteName);

            $view->with('settings', $settings);
        });
    }
}
