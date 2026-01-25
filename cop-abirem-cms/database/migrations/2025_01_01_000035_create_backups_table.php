<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename', 255);
            $table->string('file_path', 255);
            $table->bigInteger('file_size');
            $table->enum('type', ['manual', 'scheduled', 'pre_update'])->default('manual');
            $table->enum('status', ['completed', 'failed', 'in_progress'])->default('completed');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
