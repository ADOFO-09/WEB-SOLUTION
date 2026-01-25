<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('leader_id')->nullable();
            $table->string('meeting_day', 20)->nullable();
            $table->time('meeting_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('slug');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministries');
    }
};
