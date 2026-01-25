<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_years');
    }
};
