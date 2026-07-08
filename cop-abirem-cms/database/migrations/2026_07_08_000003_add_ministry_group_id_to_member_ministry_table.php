<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_ministry', function (Blueprint $table) {
            $table->foreignId('ministry_group_id')
                  ->nullable()
                  ->after('is_active')
                  ->constrained('ministry_groups')
                  ->nullOnDelete();

            $table->index('ministry_group_id');
        });
    }

    public function down(): void
    {
        Schema::table('member_ministry', function (Blueprint $table) {
            $table->dropForeign(['ministry_group_id']);
            $table->dropIndex(['ministry_group_id']);
            $table->dropColumn('ministry_group_id');
        });
    }
};
