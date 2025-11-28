<?php

namespace App\Http\Controllers;

use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use App\Models\VisualizationType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        $userRisetId = auth()->user()?->riset_id;

        $visualizationsQuery = Visualization::with(['topic.riset', 'type'])
            ->latest('updated_at');

        if ($userRisetId) {
            $visualizationsQuery->whereHas('topic', function ($query) use ($userRisetId) {
                $query->where('riset_id', $userRisetId);
            });
        }

        $visualizations = $visualizationsQuery
            ->get()
            ->map(function ($visualization) {
                return [
                    'id' => $visualization->id,
                    'title' => $visualization->title,
                    'interpretation' => Str::limit(strip_tags($visualization->interpretation ?? ''), 140),
                    'riset_name' => $visualization->topic?->riset?->name ?? '-',
                    'topic_name' => $visualization->topic?->name ?? '-',
                    'type_name' => $visualization->type?->type_name ?? '-',
                    'type_code' => $visualization->type?->type_code,
                    'created_at' => optional($visualization->created_at)->format('d M Y'),
                    'updated_at' => optional($visualization->updated_at)->format('d M Y'),
                ];
            })
            ->values();

        $totalDiseminasi = $visualizations->count();
        $totalUpdated = Visualization::whereColumn('updated_at', '>', 'created_at')
            ->when($userRisetId, function ($query) use ($userRisetId) {
                $query->whereHas('topic', function ($topicQuery) use ($userRisetId) {
                    $topicQuery->where('riset_id', $userRisetId);
                });
            })
            ->count();

        $totalDeleted = Visualization::onlyTrashed()
            ->when($userRisetId, function ($query) use ($userRisetId) {
                $query->whereHas('topic', function ($topicQuery) use ($userRisetId) {
                    $topicQuery->where('riset_id', $userRisetId);
                });
            })
            ->count();

        return Inertia::render('Dashboard', [
            'totalDiseminasi' => $totalDiseminasi,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'visualizations' => $visualizations,
        ]);
    }

    public function edit(Visualization $visualization)
    {
        $this->ensureVisualizationAccess($visualization);

        $visualization->load(['topic.riset', 'type']);

        $risetsQuery = Riset::where('is_published', true)
            ->select('id', 'name')
            ->orderBy('name');

        if ($assignedRisetId = auth()->user()?->riset_id) {
            $risetsQuery->where('id', $assignedRisetId);
        }

        $visualizationTypes = VisualizationType::select('id', 'type_code', 'type_name')
            ->orderBy('type_name')
            ->get();

        return Inertia::render('Admin/Data', [
            'risets' => $risetsQuery->get(),
            'visualizationTypes' => $visualizationTypes,
            'editingVisualization' => [
                'id' => $visualization->id,
                'topic_id' => $visualization->topic_id,
                'visualization_type_id' => $visualization->visualization_type_id,
                'title' => $visualization->title,
                'interpretation' => $visualization->interpretation,
                'chart_data' => $visualization->chart_data,
                'chart_options' => $visualization->chart_options,
                'riset_id' => $visualization->topic?->riset?->id,
                'topic_name' => $visualization->topic?->name,
                'riset_name' => $visualization->topic?->riset?->name,
                'type_code' => $visualization->type?->type_code,
            ],
        ]);
    }

    public function update(Request $request, Visualization $visualization)
    {
        $validated = $request->validate([
            'riset_id' => 'required|exists:risets,id',
            'topic_id' => 'required|exists:topics,id',
            'visualization_type_id' => 'required|exists:visualization_types,id',
            'title' => 'required|string|max:255',
            'interpretation' => 'required|string',
            'chart_data' => 'required|array',
            'chart_options' => 'nullable|array',
        ]);

        $this->ensureVisualizationAccess($visualization);
        $this->ensureRisetAccess((int) $validated['riset_id']);

        Topic::where('id', $validated['topic_id'])
            ->where('riset_id', $validated['riset_id'])
            ->firstOrFail();

        $visualization->update([
            'topic_id' => $validated['topic_id'],
            'visualization_type_id' => $validated['visualization_type_id'],
            'title' => $validated['title'],
            'interpretation' => $validated['interpretation'],
            'chart_data' => $validated['chart_data'],
            'chart_options' => $validated['chart_options'] ?? null,
            'is_published' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visualisasi berhasil diperbarui',
        ]);
    }
    
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

    public function publish(Request $request)
    {
        $validated = $request->validate([
            'riset_id' => 'required|exists:risets,id',
            'topic_id' => 'required|exists:topics,id',
            'visualization_type_id' => 'required|exists:visualization_types,id',
            'title' => 'required|string|max:255',
            'interpretation' => 'required|string',
            'chart_data' => 'required|array',
            'chart_options' => 'nullable|array',
        ]);

        $this->ensureRisetAccess((int) $validated['riset_id']);

        // Verify topic belongs to riset
        $topic = Topic::where('id', $validated['topic_id'])
            ->where('riset_id', $validated['riset_id'])
            ->firstOrFail();

        // Get visualization type
        $vizType = VisualizationType::findOrFail($validated['visualization_type_id']);

        // Get the next order number for this topic
        $nextOrder = Visualization::where('topic_id', $validated['topic_id'])
            ->max('order') + 1;

        $visualization = Visualization::create([
            'topic_id' => $validated['topic_id'],
            'visualization_type_id' => $validated['visualization_type_id'],
            'title' => $validated['title'],
            'interpretation' => $validated['interpretation'],
            'chart_data' => $validated['chart_data'],
            'chart_options' => $validated['chart_options'] ?? null,
            'order' => $nextOrder ?? 1,
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

    public function destroy(Visualization $visualization)
    {
        $this->ensureVisualizationAccess($visualization);

        $visualization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Visualisasi berhasil dihapus'
        ]);
    }

    private function ensureVisualizationAccess(Visualization $visualization): void
    {
        $risetId = $visualization->topic?->riset_id;
        if (!$risetId) {
            abort(404);
        }

        $this->ensureRisetAccess($risetId);
    }

    private function ensureRisetAccess(int $risetId): void
    {
        $user = auth()->user();

        if ($user && $user->riset_id && (int) $user->riset_id !== (int) $risetId) {
            abort(403, 'Anda tidak memiliki akses ke riset ini.');
        }
    }

    /**
     * 🔧 TAMBAHKAN METHOD INI - Generate title for visualization
     */
    private function generateTitle($typeName, $topicName)
    {
        return ucfirst($typeName) . ' - ' . $topicName;
    }
}