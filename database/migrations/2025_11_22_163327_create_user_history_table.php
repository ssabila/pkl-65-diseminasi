<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_history', function (Blueprint $table) {
            $table->id();
            $table->char('user_id', 26);
            $table->string('action', 255);
            $table->dateTime('timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status', 50);
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_history');
    }
};
