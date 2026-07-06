<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Models\News;
use App\Models\Resource;
use Illuminate\Support\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        Carbon::setLocale(app()->getLocale());

        View::composer('layouts.footer', function ($view) {
            $lang = config('app.locale');
            $view->with([
                'latestNews' => Cache::remember("latest_news_{$lang}", 300, fn() =>
                News::where('language', $lang)->where('status', 1)->orderByDesc('id')->take(4)->get()
                ),
                'latestResources' => Cache::remember("latest_resources_{$lang}", 300, fn() =>
                Resource::published()->where('language', $lang)->orderByDesc('id')->take(4)->get()
                ),
            ]);
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
