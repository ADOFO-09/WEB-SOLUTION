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
        Schema::create('ministry_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number', 30)->unique();
            $table->enum('offering_type', [
                'general',
                'welfare_contribution',
                'special_collection',
                'project',
                'other',
            ])->default('general');
            $table->decimal('amount', 12, 2);
            $table->date('offering_date');
            $table->string('description')->nullable();
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer', 'cheque'])->default('cash');
            $table->string('payment_reference', 100)->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['ministry_id', 'offering_date']);
            $table->index(['ministry_id', 'offering_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministry_offerings');
    }
};
