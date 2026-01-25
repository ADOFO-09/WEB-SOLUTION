<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->constrained();
            $table->unsignedBigInteger('member_id')->nullable()->after('role_id');
            $table->boolean('is_active')->default(true)->after('password');
            $table->boolean('must_change_password')->default(true)->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('must_change_password');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->integer('login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('login_attempts');
            $table->unsignedBigInteger('created_by')->nullable()->after('locked_until');
            $table->softDeletes();
            
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id', 'member_id', 'is_active', 'must_change_password',
                'last_login_at', 'last_login_ip', 'login_attempts', 'locked_until',
                'created_by', 'deleted_at'
            ]);
        });
    }
};
