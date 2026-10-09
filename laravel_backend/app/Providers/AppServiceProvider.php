<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

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
        // Mail notifications (OTP emails) render Blade views and need a writable compile dir.
        \Illuminate\Support\Facades\File::ensureDirectoryExists(config('view.compiled'));

        $host = request()->header('host', '');
        if (request()->hasHeader('x-forwarded-proto') || str_contains($host, 'ngrok') || str_contains($host, 'loca.lt')) {
            URL::forceScheme('https');
            $scriptName = request()->server('SCRIPT_NAME', '');
            $basePath = str_replace('/index.php', '', $scriptName);
            URL::forceRootUrl('https://' . $host . $basePath);
        }

        // This ensures the broadcasting routes (channels) are loaded
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}