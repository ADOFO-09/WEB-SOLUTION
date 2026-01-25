<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'any'])->default('any');
            $table->time('default_start_time')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
