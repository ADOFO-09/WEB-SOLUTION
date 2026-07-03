<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('welfare_benefit_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('welfare_benefit_id')->constrained('welfare_benefits')->cascadeOnDelete();
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->index('welfare_benefit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_benefit_expenses');
    }
};
