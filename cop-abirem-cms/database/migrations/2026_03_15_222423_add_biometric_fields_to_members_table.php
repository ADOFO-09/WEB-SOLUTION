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
            $table->text('fingerprint_template_1')->nullable()->after('qr_code_path');
            $table->text('fingerprint_template_2')->nullable()->after('fingerprint_template_1');
            $table->boolean('biometric_enrolled')->default(false)->after('fingerprint_template_2');
            $table->timestamp('biometric_enrolled_at')->nullable()->after('biometric_enrolled');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'fingerprint_template_1',
                'fingerprint_template_2',
                'biometric_enrolled',
                'biometric_enrolled_at',
            ]);
        });
    }
};
