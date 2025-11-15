<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['riset_id', 'name', 'slug', 'description', 'order', 'is_published'];

    public function riset()
    {
        return $this->belongsTo(Riset::class);
    }

    public function visualizations()
    {
        return $this->hasMany(Visualization::class);
    }
}
