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
        $risetsQuery = Riset::query()
            ->where('is_published', true)
            ->select('id', 'name')
            ->orderBy('name');

        if ($assignedRisetId = auth()->user()?->riset_id) {
            $risetsQuery->where('id', $assignedRisetId);
        }

        return Inertia::render('Admin/Data', [
            'risets' => $risetsQuery->get(),
            'visualizationTypes' => VisualizationType::select('id', 'type_code', 'type_name')->orderBy('type_name')->get(),
            'editingVisualization' => null,
        ]);
    }
}
