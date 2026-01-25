<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique();
            $table->foreignId('financial_year_id')->constrained();
            $table->foreignId('expense_category_id')->constrained();
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('payment_reference', 100)->nullable();
            $table->string('payee_name', 255);
            $table->string('payee_phone', 20)->nullable();
            $table->string('voucher_number', 50)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('reference_number');
            $table->index('status');
            $table->index('expense_date');
            $table->index('expense_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
