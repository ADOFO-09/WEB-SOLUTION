<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('login_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('location', 255)->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->string('failure_reason', 255)->nullable();
            
            $table->index('user_id');
            $table->index('login_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_history');
    }
};
