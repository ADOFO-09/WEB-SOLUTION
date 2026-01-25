<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->enum('category', ['general', 'financial', 'attendance', 'event', 'reminder', 'birthday']);
            $table->text('content');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
    }
};
