<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ministries')
            ->where('slug', 'mens-ministry')
            ->where('name', "Men's Ministry (Deacons)")
            ->update(['name' => "Men's Ministry"]);
    }

    public function down(): void
    {
        DB::table('ministries')
            ->where('slug', 'mens-ministry')
            ->where('name', "Men's Ministry")
            ->update(['name' => "Men's Ministry (Deacons)"]);
    }
};
