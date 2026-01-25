<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pledge_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pledge_id')->constrained()->onDelete('cascade');
            $table->string('reference_number', 50)->unique();
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('payment_reference', 100)->nullable();
            $table->string('receipt_number', 50)->unique();
            $table->boolean('sms_sent')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            
            $table->index('pledge_id');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pledge_payments');
    }
};
