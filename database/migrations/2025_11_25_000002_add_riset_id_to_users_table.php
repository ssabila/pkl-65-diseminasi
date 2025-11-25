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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'riset_id')) {
                $table->foreignId('riset_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('risets')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'riset_id')) {
                $table->dropForeign(['riset_id']);
                $table->dropColumn('riset_id');
            }
        });
    }
};

