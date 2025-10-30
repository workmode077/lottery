<?php

namespace App\Providers;

use App\Models\AdminSettings;
use App\Models\SiteSettings;
use App\Observers\MediaObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;


use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('backend.prefix', function () {
            try {
                if (Schema::hasTable('admin_settings')) {
                    return optional(AdminSettings::where('key', 'backend-prefix')->first())->value ?? 'admin-portal';
                }
            } catch (\Exception $e) {
                // Database connection failed or table doesn't exist
            }

            return 'admin-portal';
        });

        $this->app->singleton('siteSettings', function () {
            try {
                return Cache::rememberForever('siteSettings', function () {
                    return SiteSettings::first();
                });
            } catch (\Exception $e) {
                // Database connection failed or table doesn't exist
                return null;
            }
        });
        

        View::composer(['mail.admin.*', 'mail.user.*'], function ($view) {
            $view->with([
                'site_settings_common' => app('siteSettings'),
                
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Media::observe(MediaObserver::class);

        Blade::directive('settings', function ($key) {
            return "<?php echo \App\Helpers\BackendHelpers::getValueByKey($key); ?>";
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
