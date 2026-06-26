<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_recipients', function (Blueprint $table) {
            $table->text('resolved_message')->nullable()->after('recipient_name');
        });
    }

    public function down(): void
    {
        Schema::table('sms_recipients', function (Blueprint $table) {
            $table->dropColumn('resolved_message');
        });
    }
};
