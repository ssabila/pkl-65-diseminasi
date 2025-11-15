<?php

namespace App\Providers;

// Hapus semua 'use' yang tidak perlu (seperti Spatie\Health, Inertia, Storage)
use App\Models\Setting; // <-- KITA BUTUH INI
use Illuminate\Support\Facades\View; // <-- KITA BUTUH INI
use Illuminate\Support\Facades\Schema; // <-- KITA BUTUH INI
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // KOSONGKAN method ini.
        // Semua isinya (Health::checks) adalah
        // untuk fitur Health Check yang sudah kita hapus.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // INI ADALAH SOLUSI UNTUK ERROR DATABASE '1071'
        Schema::defaultStringLength(191);

        // SIMPAN BLOK INI
        // Ini untuk halaman Admin Settings yang kita simpan
        if (Schema::hasTable((new Setting())->getTable())) {
            $setting = Setting::first() ?? new Setting();
            View::share('setting', $setting);
        }
    }
}