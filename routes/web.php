<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserAccountController; // Kita simpan
use App\Http\Controllers\DashboardController; // Kita simpan
use App\Http\Controllers\Auth\LogoutController; // Kita simpan
use App\Http\Controllers\AdminSettingController; // Kita simpan

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rute-rute publik (Landing Page & Dashboard Diseminasi)
|
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/hasil-riset', [PageController::class, 'hasilRiset'])->name('hasil-riset');
Route::get('/dokumen', [PageController::class, 'dokumen'])->name('dokumen');
// TODO: Buat rute untuk Dashboard Diseminasi Anda di sini
// Contoh: Route::get('/diseminasi', [DiseminasiController::class, 'index'])->name('diseminasi.index');


/*
|--------------------------------------------------------------------------
| Admin & Authenticated Routes
|--------------------------------------------------------------------------
|
| Rute-rute yang memerlukan login admin
|
*/

// Grup untuk semua rute yang memerlukan login
Route::middleware(['web', 'auth', 'auth.session'])->group(function () {

    Route::prefix('user')->name('user.')->group(function () {
        Route::controller(UserAccountController::class)->group(function () {
            Route::get('account', 'index')->name('index');
            // Anda mungkin perlu rute lain dari template aslinya
            // jika halaman IndexPage.vue memanggilnya.
        });
    });

    // Rute Logout
    Route::post('logout', [LogoutController::class, 'destroy'])->name('logout');

    // Grup untuk semua rute admin yang dilindungi
    Route::middleware([
        'role:Super Admin', // <-- Ini adalah penjaga utama admin Anda!
        // Semua middleware yang kita hapus (disable.account, dll) sudah dibersihkan
    ])->group(function () {

        // Rute /dashboard setelah login
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Grup untuk semua rute di bawah prefix /admin
        Route::prefix('admin')->name('admin.')->group(function () {

            // Rute Pengaturan (Settings)
            Route::prefix('settings')->name('setting.')->group(function () {
                Route::controller(AdminSettingController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/update', 'update')->name('update');
                });
            });

            // -----------------------------------------------------------------
            // TODO: Buat rute-rute CRUD Diseminasi Anda di sini
            // -----------------------------------------------------------------
            // Contoh (un-comment saat Controller-nya sudah Anda buat):
            
            // Route::resource('riset', \App\Http\Controllers\RisetController::class);
            // Route::resource('topik', \App\Http\Controllers\TopicController::class);
            // Route::resource('visualisasi', \App\Http\Controllers\VisualizationController::class);

        });
    });
});
