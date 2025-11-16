<?php

namespace App\Http\Controllers;

use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use App\Models\VisualizationType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class DataController extends Controller
{
    public function index()
    {
        return Inertia::render('Data', [
            'risets' => Riset::all(),
            'visualizationTypes' => VisualizationType::all(),
        ]);
    }
}