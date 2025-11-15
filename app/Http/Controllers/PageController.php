<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /**
     * Show the terms of service page.
     */
    public function terms(): Response
    {
        return Inertia::render('Terms');
    }

    /**
     * Show the home page.
     */
    public function home(): Response
    {
        return Inertia::render('Home');
    }

    /**
     * Show the hasil riset page.
     */
    public function hasilRiset(): Response
    {
        // Nanti Orang 4 bisa nambahin logic 
        // buat ngambil data dari DB di sini.
        // Cth: $visualizations = Visualization::all();

        return Inertia::render('HasilRiset', [
            // 'visualizations' => $visualizations
        ]);
    }

    /**
     * Show the dokumen page.
     * REVISI: Data di-hardcode di Vue, jadi controller cuma render.
     */
    public function dokumen(): Response
    {
        return Inertia::render('Dokumen'); 
    }
}