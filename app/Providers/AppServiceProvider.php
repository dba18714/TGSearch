<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
        config()->set('app.timezone', 'Asia/Shanghai');
        // date_default_timezone_set('Asia/Shanghai');

        echo config('app.timezone'); // 输出当前的时区
        echo date_default_timezone_get(); // 输出 PHP 的默认时区

        // DB::statement("SET TIMEZONE TO 'UTC';");
        //
        // Carbon::setLocale('en');
        // config(['app.timezone' => 'Asia/Shanghai']);
        // date_default_timezone_set('Asia/Shanghai');
    }
}
