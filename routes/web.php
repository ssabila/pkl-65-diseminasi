<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AdminSettingController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/hasil-riset', [PageController::class, 'hasilRiset'])->name('hasil-riset');
Route::get('/dokumen', [PageController::class, 'dokumen'])->name('dokumen');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'auth.session'])->group(function () {

    // User Account
    Route::prefix('user')->name('user.')->group(function () {
        Route::controller(UserAccountController::class)->group(function () {
            Route::get('/account', 'index')->name('index');
        });
    });

    // Logout
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Super Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Super Admin'])->group(function () {

        // Dashboard (root dashboard)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Admin Panel
        |--------------------------------------------------------------------------
        */
        Route::prefix('admin')->name('admin.')->group(function () {

            /*
            |--------------------------------------------------------------
            | Dashboard Actions
            |--------------------------------------------------------------
            */
            Route::get('/dashboard/topics', [DashboardController::class, 'getTopics'])
                ->name('dashboard.topics');

            Route::post('/dashboard/upload-map', [DashboardController::class, 'uploadMapData'])
                ->name('dashboard.upload-map');

            Route::post('/dashboard/publish', [DashboardController::class, 'publish'])
                ->name('dashboard.publish');

            Route::get('/dashboard/{visualization}/edit', [DashboardController::class, 'edit'])
                ->name('dashboard.edit');

            Route::put('/dashboard/{visualization}', [DashboardController::class, 'update'])
                ->name('dashboard.update');

            Route::delete('/dashboard/{visualization}', [DashboardController::class, 'destroy'])
                ->name('dashboard.delete');

            /*
            |--------------------------------------------------------------
            | Data Page
            |--------------------------------------------------------------
            */
            Route::get('/data', [DataController::class, 'index'])->name('data');
        });
    });
});
