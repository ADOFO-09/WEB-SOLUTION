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
        Schema::table('tithes', function (Blueprint $table) {
            $table->foreignId('attendance_session_id')
                  ->nullable()
                  ->after('financial_year_id')
                  ->constrained('attendance_sessions')
                  ->nullOnDelete();

            $table->enum('collection_type', ['individual', 'session'])
                  ->default('individual')
                  ->after('attendance_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('tithes', function (Blueprint $table) {
            $table->dropForeign(['attendance_session_id']);
            $table->dropColumn(['attendance_session_id', 'collection_type']);
        });
    }
};
