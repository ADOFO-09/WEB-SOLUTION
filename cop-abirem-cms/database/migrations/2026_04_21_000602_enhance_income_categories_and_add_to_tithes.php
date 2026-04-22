<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('income_categories', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_active');
            $table->boolean('is_system')->default(false)->after('sort_order');
        });

        DB::statement("ALTER TABLE income_categories MODIFY COLUMN type ENUM('tithe','offering','donation','pledge','other','special') NOT NULL");

        Schema::table('tithes', function (Blueprint $table) {
            $table->foreignId('income_category_id')->nullable()->after('financial_year_id')
                  ->constrained('income_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tithes', function (Blueprint $table) {
            $table->dropForeign(['income_category_id']);
            $table->dropColumn('income_category_id');
        });

        Schema::table('income_categories', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'is_system']);
        });

        DB::statement("ALTER TABLE income_categories MODIFY COLUMN type ENUM('tithe','offering','donation','pledge','other') NOT NULL");
    }
};
