<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request; // Diperlukan untuk membaca query string
use App\Models\Riset;
use App\Models\Visualization;
use App\Models\Document;

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

    public function hasilRiset(Request $request) 
    {
        // LOGIC HASIL RISET (TETAP DINAMIS DARI DB)
        $risetTopics = Riset::with(['topics' => function($query) {
            $query->where('is_published', true);
        }])
        ->where('is_published', true)
        ->get();

        $topicId = $request->query('topic_id');

        if (!$topicId && $risetTopics->isNotEmpty()) {
            $firstRiset = $risetTopics->first();
            if ($firstRiset->topics->isNotEmpty()) {
                $firstTopicId = $firstRiset->topics->first()->id;
                return to_route('hasil-riset', ['topic_id' => $firstTopicId]);
            }
        }

        $activeVisualization = null;

        if ($topicId) {
            $activeVisualization = Visualization::where('topic_id', $topicId)
                ->where('is_published', true)
                ->with(['topic.riset']) 
                ->first();
            
            if ($activeVisualization) {
                $type = \App\Models\VisualizationType::find($activeVisualization->visualization_type_id);
                $activeVisualization->setRelation('type', $type);
            }
        }

        return Inertia::render('HasilRiset', [
            'risetTopics' => $risetTopics,
            'activeVisualization' => $activeVisualization,
            'selectedTopicId' => $topicId
        ]);
    }

    /**
     * Show the dokumen page.
     * PERBAIKAN: Hanya mengirim ID yang dipilih dari URL.
     */
    public function dokumen(Request $request): Response
    {
        // 1. Ambil semua dokumen
        $allDocs = Document::all();

        // 2. Grouping berdasarkan 'category' untuk Sidebar
        // Hasil: [ { id: 1, name: 'Laporan', items: [...] }, ... ]
        $categories = $allDocs->groupBy('category')->map(function ($items, $name) {
            return [
                'id' => \Illuminate\Support\Str::slug($name), // ID unik dari nama kategori
                'name' => $name,
                'items' => $items
            ];
        })->values(); // Reset array keys agar jadi array murni di JSON

        return Inertia::render('Dokumen', [
            'documentCategories' => $categories, // Data Dinamis
            'selectedDocumentId' => $request->query('document_id'),
        ]);
    }
}