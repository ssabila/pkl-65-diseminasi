<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Riset;
use App\Models\Topic;
use App\Models\Visualization;
use App\Models\VisualizationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DashboardController extends Controller
{
    public function index()
    {
        $userRisetId = Auth::user()?->riset_id;

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
        // Check access - return error response instead of abort
        $risetId = $visualization->topic?->riset_id;
        if (!$risetId) {
            return Inertia::render('Admin/Data', [
                'risets' => [],
                'visualizationTypes' => [],
                'editingVisualization' => null,
                'accessError' => 'Visualisasi tidak ditemukan.',
            ]);
        }

        $user = Auth::user();
        if ($user && $user->riset_id && (int) $user->riset_id !== (int) $risetId) {
            return Inertia::render('Admin/Data', [
                'risets' => [],
                'visualizationTypes' => [],
                'editingVisualization' => null,
                'accessError' => 'Anda tidak bisa mengedit visualisasi ini. Visualisasi ini milik riset lain.',
            ]);
        }

        $visualization->load(['topic.riset', 'type']);

        $risetsQuery = Riset::where('is_published', true)
            ->select('id', 'name')
            ->orderBy('name');

        if ($assignedRisetId = Auth::user()?->riset_id) {
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
            'accessError' => null,
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

    public function uploadHistogram(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:txt,csv,xlsx,xls|max:10240'
    ]);

    try {
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $values = [];

        if (in_array($extension, ['xlsx', 'xls'])) {
            // Parse Excel
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            
            foreach ($worksheet->toArray() as $row) {
                foreach ($row as $cell) {
                    if (is_numeric($cell)) {
                        $values[] = floatval($cell);
                    }
                }
            }
        } else {
            // Parse TXT/CSV
            $content = file_get_contents($file->getRealPath());
            $numbers = preg_split('/[\s,;\t\n\r]+/', $content);
            
            foreach ($numbers as $num) {
                $num = trim($num);
                if (is_numeric($num)) {
                    $values[] = floatval($num);
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $values,
            'message' => 'File berhasil diparse'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memproses file: ' . $e->getMessage()
        ], 422);
    }
}

    public function uploadBoxPlot(Request $request)
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
            
            // Find column indices for Kelompok and Nilai
            $groupIndex = $this->findColumnIndex($headers, ['kelompok', 'group', 'nama', 'kategori']);
            $valueIndex = $this->findColumnIndex($headers, ['nilai', 'value', 'data']);
            
            if ($groupIndex === null || $valueIndex === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'File harus memiliki kolom "Kelompok" dan "Nilai"'
                ], 422);
            }

            // Group data by Kelompok
            $groupedData = [];
            for ($i = 1; $i < count($rows); $i++) {
                if (!isset($rows[$i][$groupIndex]) || !isset($rows[$i][$valueIndex])) {
                    continue;
                }
                
                $group = trim($rows[$i][$groupIndex]);
                $value = $rows[$i][$valueIndex];
                
                if (!empty($group) && is_numeric($value)) {
                    if (!isset($groupedData[$group])) {
                        $groupedData[$group] = [];
                    }
                    $groupedData[$group][] = floatval($value);
                }
            }

            if (empty($groupedData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data valid yang ditemukan'
                ], 422);
            }

            // Calculate Tukey's statistics for each group
            $boxPlotData = [];
            $outlierData = [];
            $labels = [];
            
            foreach ($groupedData as $groupName => $values) {
                if (count($values) < 3) {
                    return response()->json([
                        'success' => false,
                        'message' => "Kelompok '$groupName' harus memiliki minimal 3 data"
                    ], 422);
                }
                
                $stats = $this->calculateTukeysBoxPlot($values);
                
                $labels[] = $groupName;
                $boxPlotData[] = [
                    'x' => $groupName,
                    'y' => [
                        $stats['min'],
                        $stats['q1'],
                        $stats['median'],
                        $stats['q3'],
                        $stats['max']
                    ]
                ];
                
                // Collect outliers for this group
                if (!empty($stats['outliers'])) {
                    foreach ($stats['outliers'] as $outlier) {
                        $outlierData[] = [
                            'x' => $groupName,
                            'y' => $outlier
                        ];
                    }
                }
            }

            // Build datasets array
            $datasets = [
                [
                    'type' => 'boxPlot',
                    'data' => $boxPlotData
                ]
            ];
            
            // Add outliers as scatter series if there are any
            if (!empty($outlierData)) {
                $datasets[] = [
                    'type' => 'scatter',
                    'name' => 'Outliers',
                    'data' => $outlierData
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => $datasets,
                    'hasOutliers' => !empty($outlierData),
                    'outlierCount' => count($outlierData)
                ],
                'total_groups' => count($boxPlotData)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Calculate Tukey's Boxplot statistics
     * Uses linear interpolation for quartiles (Excel/R method)
     * Whiskers at 1.5 × IQR from Q1 and Q3
     */
    private function calculateTukeysBoxPlot(array $values)
    {
        sort($values);
        $n = count($values);
        
        // Calculate quartiles using linear interpolation
        $q1 = $this->calculateQuartile($values, 0.25);
        $median = $this->calculateQuartile($values, 0.5);
        $q3 = $this->calculateQuartile($values, 0.75);
        
        // Calculate IQR
        $iqr = $q3 - $q1;
        
        // Calculate whisker bounds (Tukey's 1.5 × IQR rule)
        $lowerBound = $q1 - 1.5 * $iqr;
        $upperBound = $q3 + 1.5 * $iqr;
        
        // Find actual whiskers (min/max values within bounds)
        $lowerWhisker = null;
        $upperWhisker = null;
        $outliers = [];
        
        foreach ($values as $value) {
            if ($value >= $lowerBound && $value <= $upperBound) {
                // Value is within whisker bounds
                if ($lowerWhisker === null || $value < $lowerWhisker) {
                    $lowerWhisker = $value;
                }
                if ($upperWhisker === null || $value > $upperWhisker) {
                    $upperWhisker = $value;
                }
            } else {
                // Value is an outlier
                $outliers[] = $value;
            }
        }
        
        return [
            'min' => $lowerWhisker ?? $values[0],
            'q1' => $q1,
            'median' => $median,
            'q3' => $q3,
            'max' => $upperWhisker ?? $values[$n - 1],
            'outliers' => $outliers
        ];
    }

    /**
     * Calculate quartile using linear interpolation (Excel/R method)
     * 
     * @param array $sortedValues Sorted array of values
     * @param float $p Percentile (0.25 for Q1, 0.5 for median, 0.75 for Q3)
     * @return float Calculated quartile value
     */
    private function calculateQuartile(array $sortedValues, float $p)
    {
        $n = count($sortedValues);
        $index = $p * ($n - 1);
        $lowerIndex = floor($index);
        $upperIndex = ceil($index);
        $fraction = $index - $lowerIndex;
        
        // If index is exactly on a data point
        if ($lowerIndex == $upperIndex) {
            return $sortedValues[$lowerIndex];
        }
        
        // Linear interpolation between two data points
        return $sortedValues[$lowerIndex] + $fraction * ($sortedValues[$upperIndex] - $sortedValues[$lowerIndex]);
    }

    /**
     * Find column index by searching for possible column names
     */
    private function findColumnIndex(array $headers, array $possibleNames)
    {
        foreach ($possibleNames as $name) {
            $index = array_search($name, $headers);
            if ($index !== false) {
                return $index;
            }
        }
        return null;
    }


    public function uploadMapData(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'map_type' => 'required|in:heatmap,choropleth'
        ]);

        try {
            $file = $request->file('file');
            $mapType = $request->input('map_type');
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong atau format tidak valid'
                ], 422);
            }

            $rows = $data[0];
            $headers = array_map('strtolower', array_map('trim', $rows[0]));

            if ($mapType === 'heatmap') {
                return $this->processHeatmapData($rows, $headers);
            } else {
                return $this->processchoroplethData($rows, $headers);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processHeatmapData($rows, $headers)
    {
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
    }

    private function processchoroplethData($rows, $headers)
    {
        // Load GeoJSON data
        $geojsonPath = public_path('geojson/yogyakarta.geojson');
        $geojsonData = null;
        
        if (file_exists($geojsonPath)) {
            $geojsonContent = file_get_contents($geojsonPath);
            $geojsonData = json_decode($geojsonContent, true);
        }

        // Required columns for choropleth: id, region_name/nama_daerah
        $idIndex = null;
        $regionNameIndex = null;

        // Find ID column
        foreach (['id', 'region_id', 'area_id'] as $idCol) {
            if (in_array($idCol, $headers)) {
                $idIndex = array_search($idCol, $headers);
                break;
            }
        }

        // Find region name column
        foreach (['region_name', 'nama_daerah', 'daerah', 'wilayah', 'name'] as $nameCol) {
            if (in_array($nameCol, $headers)) {
                $regionNameIndex = array_search($nameCol, $headers);
                break;
            }
        }

        if ($idIndex === null || $regionNameIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'File harus memiliki kolom ID dan nama daerah (region_name/nama_daerah)'
            ], 422);
        }

        // Identify variable columns (exclude id, region_name, lat, lng)
        $excludeColumns = ['id', 'region_id', 'area_id', 'region_name', 'nama_daerah', 'daerah', 'wilayah', 'name', 'latitude', 'longitude', 'lat', 'lng'];
        $variableColumns = array_diff($headers, $excludeColumns);
        $variableColumns = array_filter($variableColumns, function($col) {
            return !empty(trim($col));
        });

        if (empty($variableColumns)) {
            return response()->json([
                'success' => false,
                'message' => 'File harus memiliki minimal satu kolom variabel (selain ID dan nama daerah)'
            ], 422);
        }

        $choroplethData = [];
        for ($i = 1; $i < count($rows); $i++) {
            if (count($rows[$i]) > max($idIndex, $regionNameIndex)) {
                $regionId = trim($rows[$i][$idIndex]);
                $regionName = trim($rows[$i][$regionNameIndex]);

                if (!empty($regionId) && !empty($regionName)) {
                    $variables = [];
                    foreach ($variableColumns as $varCol) {
                        $varIndex = array_search($varCol, $headers);
                        if ($varIndex !== false && isset($rows[$i][$varIndex])) {
                            $variables[$varCol] = $rows[$i][$varIndex];
                        }
                    }

                    // Add optional lat/lng for better mapping
                    $lat = null;
                    $lng = null;
                    if (in_array('latitude', $headers) && in_array('longitude', $headers)) {
                        $latIdx = array_search('latitude', $headers);
                        $lngIdx = array_search('longitude', $headers);
                        $lat = isset($rows[$i][$latIdx]) ? floatval($rows[$i][$latIdx]) : null;
                        $lng = isset($rows[$i][$lngIdx]) ? floatval($rows[$i][$lngIdx]) : null;
                    }

                    // Find matching GeoJSON feature
                    $geoFeature = null;
                    if ($geojsonData && isset($geojsonData['features'])) {
                        foreach ($geojsonData['features'] as $feature) {
                            if (isset($feature['properties']['name']) && 
                                (strtolower($feature['properties']['name']) === strtolower($regionName) ||
                                 $feature['properties']['id'] === $regionId)) {
                                $geoFeature = $feature;
                                break;
                            }
                        }
                    }

                    // Flatten structure for easier frontend access
                    $regionData = [
                        'id' => $regionId,
                        'nama_daerah' => $regionName,
                        'lat' => $lat,
                        'lng' => $lng,
                        'geometry' => $geoFeature ? $geoFeature['geometry'] : null,
                        'geojson_id' => $geoFeature ? $geoFeature['properties']['id'] : null
                    ];
                    
                    // Add all variables directly to the region data
                    foreach ($variables as $varName => $varValue) {
                        $regionData[$varName] = $varValue;
                    }
                    
                    $choroplethData[] = $regionData;
                }
            }
        }

        if (empty($choroplethData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data valid yang ditemukan'
            ], 422);
        }

        // Prepare preview data (first 10 rows of CSV)
        $previewHeaders = $headers;
        $previewData = [];
        for ($i = 1; $i < min(count($rows), 11); $i++) { // First 10 data rows
            if (isset($rows[$i])) {
                $previewData[] = $rows[$i];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $choroplethData,
            'variables' => array_values($variableColumns),
            'total_regions' => count($choroplethData),
            'geojson' => $geojsonData,
            'preview_headers' => $previewHeaders,
            'preview_data' => $previewData
        ]);
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
        $user = Auth::user();

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
