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

        // Idempotent: skip if FK was already applied (prevents errno 121 on re-run)
        $fkExists = \Illuminate\Support\Facades\DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'ministries'
              AND CONSTRAINT_NAME = 'ministries_leader_id_foreign'
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        if (!empty($fkExists)) {
            return;
        }

        $indexExists = \Illuminate\Support\Facades\DB::select("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME  = 'ministries'
              AND INDEX_NAME  = 'ministries_leader_id_index'
            LIMIT 1
        ");

        Schema::table('ministries', function (Blueprint $table) use ($indexExists) {
            if (empty($indexExists)) {
                $table->index('leader_id');
            }
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
