<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_type_id')->constrained();
            $table->foreignId('ministry_id')->nullable()->constrained()->nullOnDelete();
            $table->date('service_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('theme', 255)->nullable();
            $table->string('preacher', 255)->nullable();
            $table->integer('total_members')->default(0);
            $table->integer('total_visitors')->default(0);
            $table->integer('total_children')->default(0);
            $table->integer('total_attendance')->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            $table->index('service_date');
            $table->index('status');
            $table->unique(['service_type_id', 'service_date', 'ministry_id'], 'unique_session');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
