<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
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
        $risetTopics = Riset::with(['topics' => function($query) {
            $query->where('is_published', true)->orderBy('order');
        }])->where('is_published', true)->get();

        $topicId = $request->query('topic_id');

        // Auto redirect ke topik pertama
        if (!$topicId && $risetTopics->isNotEmpty()) {
            $firstRiset = $risetTopics->first();
            if ($firstRiset->topics->isNotEmpty()) {
                $firstTopicId = $firstRiset->topics->first()->id;
                return to_route('hasil-riset', ['topic_id' => $firstTopicId]);
            }
        }

        $visualizations = [];
        $activeTopic = null;

        if ($topicId) {
            $visualizations = Visualization::where('topic_id', $topicId)
                ->where('is_published', true)
                ->with(['type', 'topic.riset'])
                ->orderBy('order', 'asc')
                ->get();
            
            if ($visualizations->isNotEmpty()) {
                $activeTopic = $visualizations->first()->topic;
            } else {
                $activeTopic = \App\Models\Topic::with('riset')->find($topicId);
            }
        }

        return Inertia::render('HasilRiset', [
            'risetTopics' => $risetTopics,
            'visualizations' => $visualizations,
            'activeTopic' => $activeTopic,
            'selectedTopicId' => $topicId
        ]);
    }

    public function dokumen(Request $request): Response
    {
        // 1. AMBIL DATA UNTUK SIDEBAR (WAJIB)
        $risetTopics = Riset::with(['topics' => function($query) {
            $query->where('is_published', true)->orderBy('order');
        }])->where('is_published', true)->get();

        // 2. AMBIL DOKUMEN
        $allDocs = Document::orderBy('created_at', 'desc')->get();
        
        $categories = $allDocs->groupBy('category')->map(function ($items, $name) {
            return [
                'id' => \Illuminate\Support\Str::slug($name),
                'name' => $name,
                'items' => $items
            ];
        })->values();

        return Inertia::render('Dokumen', [
            'risetTopics' => $risetTopics, // Pastikan ini dikirim!
            'documentCategories' => $categories,
            'selectedDocumentId' => $request->query('document_id'),
        ]);
    }
}