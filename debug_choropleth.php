<?php
// Debug specific choropleth visualization
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Visualization;

echo "=== CHOROPLETH VISUALIZATION DETAIL ===\n";
$vis = Visualization::with(['type', 'topic'])->find(12);

if ($vis) {
    echo "ID: " . $vis->id . "\n";
    echo "Title: " . $vis->title . "\n";
    echo "Type Code: " . $vis->type->type_code . "\n";
    echo "Type Name: " . $vis->type->type_name . "\n";
    echo "Topic ID: " . $vis->topic_id . "\n";
    echo "Published: " . ($vis->is_published ? 'Yes' : 'No') . "\n";
    
    echo "\n=== CHART DATA STRUCTURE ===\n";
    $chartData = $vis->chart_data;
    
    if (is_array($chartData)) {
        foreach ($chartData as $key => $value) {
            if ($key === 'regions') {
                echo "$key: " . count($value) . " regions\n";
                if (count($value) > 0) {
                    echo "  First region keys: " . implode(', ', array_keys($value[0])) . "\n";
                    echo "  First region nama_daerah: " . $value[0]['nama_daerah'] . "\n";
                }
            } elseif ($key === 'variables') {
                echo "$key: " . implode(', ', $value) . "\n";
            } elseif ($key === 'geojson') {
                echo "$key: " . (is_array($value) ? 'Present (' . count($value['features'] ?? []) . ' features)' : 'NOT_ARRAY') . "\n";
            } else {
                echo "$key: " . $value . "\n";
            }
        }
    } else {
        echo "Chart data is not an array!\n";
        var_dump($chartData);
    }
} else {
    echo "Visualization not found!\n";
}
?>