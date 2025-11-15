<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visualizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->foreignId('visualization_type_id')->constrained('visualization_types')->onDelete('cascade');
            $table->string('title');
            $table->json('chart_data')->nullable();
            $table->json('chart_options')->nullable();
            $table->text('interpretation')->nullable();
            $table->string('data_source')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visualizations');
    }
};
