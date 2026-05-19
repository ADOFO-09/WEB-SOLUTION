<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ministry type — identifies home cells and bible study groups for
        // auto-populating section 3 of the monthly statistical report.
        Schema::table('ministries', function (Blueprint $table) {
            $table->enum('type', [
                'general', 'home_cell', 'bible_study', 'prayer',
                'youth', 'women', 'men', 'children', 'choir', 'evangelism', 'other',
            ])->default('general')->after('is_active');
        });

        // Seed types for the ten default ministries
        DB::table('ministries')->where('id', 1)->update(['type' => 'men']);
        DB::table('ministries')->where('id', 2)->update(['type' => 'women']);
        DB::table('ministries')->where('id', 3)->update(['type' => 'youth']);
        DB::table('ministries')->where('id', 4)->update(['type' => 'children']);
        DB::table('ministries')->where('id', 5)->update(['type' => 'choir']);
        DB::table('ministries')->where('id', 8)->update(['type' => 'evangelism']);
        DB::table('ministries')->where('id', 9)->update(['type' => 'prayer']);

        // date_left tracks when a member was transferred out, died, or became inactive.
        // Used to attribute the event to the correct monthly report period.
        Schema::table('members', function (Blueprint $table) {
            $table->date('date_left')->nullable()->after('date_joined');
        });
    }

    public function down(): void
    {
        Schema::table('ministries', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('date_left');
        });
    }
};
