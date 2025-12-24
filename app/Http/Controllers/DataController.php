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
<<<<<<< HEAD
        $risetsQuery = Riset::query()
            ->where('is_published', true)
            ->select('id', 'name')
            ->orderBy('name');

        if ($assignedRisetId = Auth::user()?->riset_id) {
            $risetsQuery->where('id', $assignedRisetId);
        }

        return Inertia::render('Admin/Data', [
            'risets' => $risetsQuery->get(),
            'visualizationTypes' => VisualizationType::select('id', 'type_code', 'type_name')->orderBy('type_name')->get(),
            'editingVisualization' => null,
            'accessError' => null,
=======
        $risets = Riset::whereIn('id', [1, 2, 5])->where('is_published', true)->orderBy('id')->get();
        
        return Inertia::render('Data', [
            'risets' => $risets,
            'visualizationTypes' => VisualizationType::all(),
>>>>>>> 53c998f (menyimpan perbaikan merge)
        ]);
    }
}
