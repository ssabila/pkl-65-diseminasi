<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisualizationType;

class ChoroplethTypeSeeder extends Seeder
{
    public function run()
    {
        VisualizationType::updateOrCreate(
            ['type_code' => 'choropleth'], 
            ['type_name' => 'Choropleth Map']
        );
        
        $this->command->info('Choropleth type added successfully');
    }
}