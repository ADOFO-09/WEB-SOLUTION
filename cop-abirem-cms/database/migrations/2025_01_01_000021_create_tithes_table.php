<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tithes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique();
            $table->foreignId('member_id')->constrained();
            $table->foreignId('financial_year_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('payment_reference', 100)->nullable();
            $table->date('month_for');
            $table->string('receipt_number', 50)->unique();
            $table->text('notes')->nullable();
            $table->boolean('sms_sent')->default(false);
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('reference_number');
            $table->index('member_id');
            $table->index('payment_date');
            $table->index('month_for');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tithes');
    }
};
