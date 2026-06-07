<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Paksa timezone & locale mengikuti .env (override cache config)
        config([
            'app.timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
            'app.locale' => env('APP_LOCALE', 'id'),
        ]);
        date_default_timezone_set(config('app.timezone'));
        \Carbon\Carbon::setLocale(config('app.locale'));

        // PostgreSQL: set session timezone supaya NOW() & timestamps konsisten
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("SET TIME ZONE 'Asia/Jakarta'");
        }
    }
}
