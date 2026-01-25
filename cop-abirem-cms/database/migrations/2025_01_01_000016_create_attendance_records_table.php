<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('visitor_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('check_in_time')->useCurrent();
            $table->timestamp('check_out_time')->nullable();
            $table->enum('attendance_method', ['manual', 'qr_code', 'biometric', 'face_recognition'])->default('manual');
            $table->boolean('is_late')->default(false);
            $table->foreignId('marked_by')->constrained('users');
            $table->timestamps();
            
            $table->index('session_id');
            $table->index('member_id');
            $table->unique(['session_id', 'member_id'], 'unique_member_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
