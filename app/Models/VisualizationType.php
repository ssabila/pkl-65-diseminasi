<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class VisualizationType extends Model
{
    use HasFactory;

    protected $fillable = ['type_code', 'type_name'];

    public function visualizations()
    {
        return $this->hasMany(Visualization::class);
    }
}

