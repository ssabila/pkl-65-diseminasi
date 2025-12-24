<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notices', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dismissible')->default(false);
            $table->string('type')->default('info');
            $table->dateTime('visible_from')->nullable();
            $table->dateTime('expires_at')->nullable();

            // PERBAIKAN: Menyesuaikan dengan tabel Users yang pakai BIGINT
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notices');
    }
};