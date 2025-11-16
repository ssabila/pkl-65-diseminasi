<?php

namespace App\Http\Controllers;

use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use App\Models\VisualizationType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $risets = Riset::where('is_published', true)
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get();

        $visualizationTypes = VisualizationType::select('id', 'type_code', 'type_name')
            ->orderBy('type_name')
            ->get();

        return Inertia::render('Dashboard', [
            'risets' => $risets,
            'visualizationTypes' => $visualizationTypes,
        ]);
    }

    /**
     * Get topics based on selected riset
     */
    public function getTopics(Request $request)
    {
        $request->validate([
            'riset_id' => 'required|exists:risets,id'
        ]);

        $topics = Topic::where('riset_id', $request->riset_id)
            ->where('is_published', true)
            ->select('id', 'name', 'slug', 'order')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'topics' => $topics
        ]);
    }

    /**
     * Upload and process CSV/Excel file for map
     */
    public function uploadMapData(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong atau format tidak valid'
                ], 422);
            }

            $rows = $data[0];
            $headers = array_map('strtolower', array_map('trim', $rows[0]));
            
            // Check required columns
            $requiredColumns = ['latitude', 'longitude', 'density'];
            $missingColumns = array_diff($requiredColumns, $headers);
            
            if (!empty($missingColumns)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File harus memiliki kolom: latitude, longitude, density'
                ], 422);
            }

            $latIndex = array_search('latitude', $headers);
            $lngIndex = array_search('longitude', $headers);
            $densityIndex = array_search('density', $headers);

            $mapData = [];
            for ($i = 1; $i < count($rows); $i++) {
                if (count($rows[$i]) > max($latIndex, $lngIndex, $densityIndex)) {
                    $lat = floatval($rows[$i][$latIndex]);
                    $lng = floatval($rows[$i][$lngIndex]);
                    $density = floatval($rows[$i][$densityIndex]);

                    if ($lat && $lng && $density) {
                        $mapData[] = [
                            'lat' => $lat,
                            'lng' => $lng,
                            'density' => $density
                        ];
                    }
                }
            }

            if (empty($mapData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data valid yang ditemukan'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $mapData,
                'total_points' => count($mapData)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish/Store visualization
     */
    public function publish(Request $request)
    {
        $validated = $request->validate([
            'riset_id' => 'required|exists:risets,id',
            'topic_id' => 'required|exists:topics,id',
            'visualization_type_id' => 'required|exists:visualization_types,id',
            'interpretation' => 'required|string',
            'chart_data' => 'required|array',
            'chart_options' => 'nullable|array',
        ]);

        // Verify topic belongs to riset
        $topic = Topic::where('id', $validated['topic_id'])
            ->where('riset_id', $validated['riset_id'])
            ->firstOrFail();

        // Get visualization type
        $vizType = VisualizationType::findOrFail($validated['visualization_type_id']);

        // Generate title based on type
        $title = $this->generateTitle($vizType->type_name, $topic->name);

        // Get the next order number for this topic
        $nextOrder = Visualization::where('topic_id', $validated['topic_id'])
            ->max('order') + 1;

        $visualization = Visualization::create([
            'topic_id' => $validated['topic_id'],
            'visualization_type_id' => $validated['visualization_type_id'],
            'title' => $title,
            'interpretation' => $validated['interpretation'],
            'chart_data' => $validated['chart_data'],
            'chart_options' => $validated['chart_options'] ?? null,
            'order' => $nextOrder,
            'is_published' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visualisasi berhasil dipublikasikan',
            'visualization' => [
                'id' => $visualization->id,
                'title' => $visualization->title,
            ]
        ]);
    }

    /**
     * Generate title for visualization
     */
    private function generateTitle($typeName, $topicName)
    {
        return $typeName . ' - ' . $topicName;
    }
}