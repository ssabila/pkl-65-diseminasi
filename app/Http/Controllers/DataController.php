<?php

namespace App\Http\Controllers;

use App\Models\Riset;
use App\Models\VisualizationType;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataController extends Controller

{
    // Untuk route /admin/data - Menampilkan FORM INPUT
    public function index()
    {
        $risets = Riset::whereIn('id', [1, 2, 5])->where('is_published', true)->orderBy('id')->get();
        
        return Inertia::render('Data', [
            'risets' => $risets,
            'visualizationTypes' => VisualizationType::all(),
        ]);
    }
}
