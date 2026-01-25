<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->decimal('depreciation_rate', 5, 2)->default(0.00);
            $table->enum('depreciation_method', ['straight_line', 'declining_balance', 'none'])->default('straight_line');
            $table->integer('useful_life_years')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
