<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\Riset;
use App\Models\Visualization;

class PageController extends Controller
{
    public function terms(): Response
    {
        return Inertia::render('Terms');
    }

    public function home(): Response
    {
        return Inertia::render('Home');
    }

    /**
     * PERBAIKAN: Saya hapus ": Response" di sini.
     * Sekarang fungsi ini bebas mau me-return halaman ATAU redirect.
     */
    public function hasilRiset(Request $request) 
    {
        // 1. Ambil Data Sidebar
        $risetTopics = Riset::with(['topics' => function($query) {
            $query->where('is_published', true);
        }])
        ->where('is_published', true)
        ->get();

        // 2. Cek URL & Auto-Select
        $topicId = $request->query('topic_id');

        // Kalau gak ada topik dipilih, otomatis pilih yang pertama biar gak kosong
        if (!$topicId && $risetTopics->isNotEmpty()) {
            $firstRiset = $risetTopics->first();
            if ($firstRiset->topics->isNotEmpty()) {
                $firstTopicId = $firstRiset->topics->first()->id;
                // Ini yang bikin error tadi. Sekarang sudah aman!
                return to_route('hasil-riset', ['topic_id' => $firstTopicId]);
            }
        }

        $activeVisualization = null;

        if ($topicId) {
            $activeVisualization = Visualization::where('topic_id', $topicId)
                ->where('is_published', true)
                ->with(['topic.riset']) 
                ->first();
        }

        return Inertia::render('HasilRiset', [
            'risetTopics' => $risetTopics,
            'activeVisualization' => $activeVisualization,
            'selectedTopicId' => $topicId
        ]);
    }

    public function dokumen(): Response
    {
        return Inertia::render('Dokumen'); 
    }
}