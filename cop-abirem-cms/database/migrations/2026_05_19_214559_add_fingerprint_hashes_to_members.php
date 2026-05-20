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
        Schema::table('members', function (Blueprint $table) {
            // SHA-256 hash of the raw template — stored plain (not encrypted) so we
            // can do a DB-level uniqueness check without decrypting every row.
            $table->string('fingerprint_hash_1', 64)->nullable()->unique()->after('fingerprint_template_1');
            $table->string('fingerprint_hash_2', 64)->nullable()->unique()->after('fingerprint_template_2');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['fingerprint_hash_1', 'fingerprint_hash_2']);
        });
    }
};
