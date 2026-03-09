<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->string('qr_token')->unique()->nullable()->after('id');
            $table->timestamp('qr_expires_at')->nullable()->after('qr_token');
            $table->boolean('allow_qr_attendance')->default(true)->after('qr_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'qr_expires_at', 'allow_qr_attendance']);
        });
    }
};
