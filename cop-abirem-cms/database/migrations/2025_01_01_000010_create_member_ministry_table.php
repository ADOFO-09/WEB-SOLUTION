<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_ministry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('ministry_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['member', 'leader', 'assistant_leader'])->default('member');
            $table->date('joined_date');
            $table->date('left_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['member_id', 'ministry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_ministry');
    }
};
