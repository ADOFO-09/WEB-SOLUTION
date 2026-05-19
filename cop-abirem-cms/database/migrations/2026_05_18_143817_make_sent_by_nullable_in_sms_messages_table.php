<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            // Allow automated messages (cron/birthday SMS) that have no logged-in user
            $table->foreignId('sent_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            $table->foreignId('sent_by')->nullable(false)->change();
        });
    }
};
