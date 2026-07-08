<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullify any orphaned leader_id values before adding the FK
        \Illuminate\Support\Facades\DB::statement(
            'UPDATE ministries SET leader_id = NULL WHERE leader_id IS NOT NULL AND leader_id NOT IN (SELECT id FROM members)'
        );

        Schema::table('ministries', function (Blueprint $table) {
            $table->index('leader_id');
            $table->foreign('leader_id')
                  ->references('id')
                  ->on('members')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropIndex(['leader_id']);
        });
    }
};
