<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->unsignedBigInteger('pledge_id')->nullable()->after('session_id');
            $table->foreign('pledge_id')->references('id')->on('pledges')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropForeign(['pledge_id']);
            $table->dropColumn('pledge_id');
        });
    }
};
