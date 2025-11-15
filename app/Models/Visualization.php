<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visualization extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id', 'visualization_type_id', 'title', 'chart_data',
        'chart_options', 'interpretation', 'data_source', 'order', 'is_published'
    ];

    protected $casts = [
        'chart_data' => 'array',
        'chart_options' => 'array',
        'is_published' => 'boolean'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function type()
    {
        return $this->belongsTo(VisualizationType::class, 'visualization_type_id');
    }
}

