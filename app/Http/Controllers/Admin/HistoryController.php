<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use OwenIt\Auditing\Models\Audit;

class HistoryController extends Controller
{
    public function index()
    {
        $logs = Audit::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user?->name ?? 'Unknown User',
                    'action' => $log->event ?: ($log->description ?? '-'),
                    'created_at' => $log->created_at?->format('Y-m-d H:i:s'),
                ];
            });

        return inertia('Admin/History', [
            'rows' => $logs,
        ]);
    }
}
