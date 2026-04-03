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

        //Paginator::useBootstrap();
        Paginator::useTailwind();

        // Share categories with all views
        View::composer('*', function ($view) {
            $categories = cache()->remember('categories_menu', 3600, function () {
                return Category::with(['children' => function ($query) {
                    $query->with('children')->active()->ordered();
                }])
                    ->active()
                    ->parents()
                    ->featured()
                    ->ordered()
                    ->get();
            });

            $allCategories = cache()->remember('all_categories_menu', 3600, function () {
                return Category::with(['children' => function ($query) {
                    $query->with('children')->active()->ordered();
                }])
                    ->active()
                    ->parents()
                    ->ordered()
                    ->get();
            });

            $settings = cache()->remember('site_settings', 3600, function () {
                return \App\Models\Setting::select('value', 'key')->pluck('value', 'key');
            });

            $siteName = $settings['site_name'] ?? null;

            View::share('siteName', $siteName);

            $view->with('menuCategories', $categories);
            $view->with('allMenuCategories', $allCategories);
            $view->with('settings', $settings);
        });
    }
}
