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
        Schema::create('ministry_welfare_benefit_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_welfare_benefit_id');
            $table->foreign('ministry_welfare_benefit_id', 'mwbe_benefit_fk')->references('id')->on('ministry_welfare_benefits')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministry_welfare_benefit_expenses');
    }
};
