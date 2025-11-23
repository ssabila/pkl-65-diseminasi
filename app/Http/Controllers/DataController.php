<?php

namespace App\Http\Controllers;

use App\Models\Riset;
use App\Models\VisualizationType;
use Inertia\Inertia;

class DataController extends Controller

{
    // Untuk route /admin/data - Menampilkan FORM INPUT
    public function index()
    {
        return Inertia::render('Admin/Data', [
            'risets' => Riset::all(),
            'visualizationTypes' => VisualizationType::all(),
        ]);
    }
}
