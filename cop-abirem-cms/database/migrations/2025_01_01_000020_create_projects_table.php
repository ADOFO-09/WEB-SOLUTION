<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->decimal('target_amount', 15, 2)->default(0.00);
            $table->decimal('amount_raised', 15, 2)->default(0.00);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
