<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministry_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->foreignId('leader_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['ministry_id', 'name']);
            $table->index('ministry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ministry_groups');
    }
};
