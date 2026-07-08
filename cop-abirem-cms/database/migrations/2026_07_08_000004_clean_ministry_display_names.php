<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ministries')
            ->where('slug', 'womens-ministry')
            ->where('name', "Women's Ministry (Deaconesses)")
            ->update(['name' => "Women's Ministry"]);
    }

    public function down(): void
    {
        DB::table('ministries')
            ->where('slug', 'womens-ministry')
            ->where('name', "Women's Ministry")
            ->update(['name' => "Women's Ministry (Deaconesses)"]);
    }
};
