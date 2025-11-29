<?php
// Simple debug script to check visualizations
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Visualization;

echo "=== VISUALIZATIONS IN DATABASE ===\n";
$visualizations = Visualization::with(['type', 'topic'])->get();

echo "Total: " . $visualizations->count() . "\n\n";

foreach ($visualizations as $vis) {
    echo "ID: " . $vis->id . "\n";
    echo "Title: " . $vis->title . "\n";
    echo "Type Code: " . ($vis->type ? $vis->type->type_code : 'NULL') . "\n";
    echo "Topic ID: " . $vis->topic_id . "\n";
    echo "Published: " . ($vis->is_published ? 'Yes' : 'No') . "\n";
    echo "Chart Data Keys: " . (is_array($vis->chart_data) ? implode(', ', array_keys($vis->chart_data)) : 'NOT_ARRAY') . "\n";
    echo "---\n";
}
?>