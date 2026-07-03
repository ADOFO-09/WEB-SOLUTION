<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('welfare_enrolled')->default(true)->after('notes');
            $table->date('welfare_start_date')->nullable()->after('welfare_enrolled');
            $table->boolean('funeral_enrolled')->default(true)->after('welfare_start_date');
            $table->date('funeral_start_date')->nullable()->after('funeral_enrolled');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['welfare_enrolled', 'welfare_start_date', 'funeral_enrolled', 'funeral_start_date']);
        });
    }
};
