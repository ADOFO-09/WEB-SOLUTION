<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['tithes', 'offerings', 'donations', 'expenses'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->enum('ledger_status', ['active', 'voided', 'adjusted'])->default('active')->after('notes');
                $table->foreignId('voided_by')->nullable()->after('ledger_status')->constrained('users')->nullOnDelete();
                $table->timestamp('voided_at')->nullable()->after('voided_by');
                $table->string('void_reason', 500)->nullable()->after('voided_at');
                $table->unsignedBigInteger('adjusted_by_id')->nullable()->after('void_reason');
                $table->unsignedBigInteger('adjusts_entry_id')->nullable()->after('adjusted_by_id');
                $table->boolean('is_adjustment')->default(false)->after('adjusts_entry_id');
            });
        }

        // Add notes column to expenses if it doesn't exist (it may not have one)
        if (!Schema::hasColumn('expenses', 'notes')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->text('notes')->nullable()->before('ledger_status');
            });
        }

        Schema::create('ledger_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entry_type', 20);
            $table->unsignedBigInteger('entry_id');
            $table->enum('action', ['created', 'updated', 'voided', 'adjusted', 'restored']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('reason', 500)->nullable();
            $table->foreignId('performed_by')->constrained('users');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['entry_type', 'entry_id']);
            $table->index('performed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_audit_logs');

        $cols = ['ledger_status', 'voided_by', 'voided_at', 'void_reason',
                 'adjusted_by_id', 'adjusts_entry_id', 'is_adjustment'];

        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($cols) {
                $t->dropForeign(['voided_by']);
                $t->dropColumn($cols);
            });
        }
    }
};
