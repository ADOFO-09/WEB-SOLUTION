<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('welfare_rates', function (Blueprint $table) {
            $table->boolean('is_current')->default(false)->after('notes');
        });

        Schema::table('ministry_welfare_rates', function (Blueprint $table) {
            $table->boolean('is_current')->default(false)->after('notes');
        });

        // Mark the most recent global welfare rate as current
        $latestId = DB::table('welfare_rates')->orderByDesc('effective_from')->value('id');
        if ($latestId) {
            DB::table('welfare_rates')->where('id', $latestId)->update(['is_current' => true]);
        }

        // Mark the most recent per-ministry rate as current
        $ministryIds = DB::table('ministry_welfare_rates')->distinct()->pluck('ministry_id');
        foreach ($ministryIds as $ministryId) {
            $latestId = DB::table('ministry_welfare_rates')
                ->where('ministry_id', $ministryId)
                ->orderByDesc('effective_from')
                ->value('id');
            if ($latestId) {
                DB::table('ministry_welfare_rates')->where('id', $latestId)->update(['is_current' => true]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('welfare_rates', function (Blueprint $table) {
            $table->dropColumn('is_current');
        });

        Schema::table('ministry_welfare_rates', function (Blueprint $table) {
            $table->dropColumn('is_current');
        });
    }
};
