<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('financial_year_id')->constrained();
            $table->foreignId('income_category_id')->constrained();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('donation_type', ['monetary', 'in_kind'])->default('monetary');
            $table->text('in_kind_description')->nullable();
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'cheque', 'in_kind'])->default('cash');
            $table->string('payment_reference', 100)->nullable();
            $table->string('receipt_number', 50)->unique();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('sms_sent')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('reference_number');
            $table->index('payment_date');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
