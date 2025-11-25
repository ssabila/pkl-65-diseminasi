<?php

namespace App\Http\Controllers;

use App\Models\Visualization;
use App\Models\Riset;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index()
    {
        $totalDiseminasi = Visualization::count();
        $totalUpdated = Visualization::whereNotNull('updated_at')->count();
        $totalDeleted = Visualization::onlyTrashed()->count();

        $latest = Visualization::with(['topic.riset'])
            ->orderBy('created_at','desc')
            ->limit(50)
            ->get();

        return Inertia::render('AdminDashboard', [
            'latest' => $latest,
            'totalDiseminasi' => $totalDiseminasi,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
        ]);
    }
}
