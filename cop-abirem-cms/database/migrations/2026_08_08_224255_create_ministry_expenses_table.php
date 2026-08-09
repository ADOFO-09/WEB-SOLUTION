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
        Schema::create('ministry_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number', 30)->unique();
            $table->enum('category', [
                'welfare_payment',
                'program',
                'materials',
                'transport',
                'communication',
                'admin',
                'other',
            ])->default('other');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('description');
            $table->string('paid_to')->nullable();
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('payment_reference', 100)->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['ministry_id', 'expense_date']);
            $table->index(['ministry_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministry_expenses');
    }
};
