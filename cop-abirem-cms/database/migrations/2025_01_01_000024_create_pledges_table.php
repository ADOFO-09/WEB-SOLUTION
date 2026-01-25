<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pledges', function (Blueprint $table) {
            $table->id();
            $table->string('pledge_number', 50)->unique();
            $table->foreignId('member_id')->constrained();
            $table->foreignId('financial_year_id')->constrained();
            $table->foreignId('income_category_id')->constrained();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0.00);
            $table->date('pledge_date');
            $table->date('due_date');
            $table->enum('payment_frequency', ['one_time', 'weekly', 'monthly', 'quarterly', 'annually'])->default('monthly');
            $table->enum('status', ['active', 'completed', 'cancelled', 'defaulted'])->default('active');
            $table->timestamp('reminder_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('pledge_number');
            $table->index('member_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pledges');
    }
};
