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
        Schema::create('ministry_welfare_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ministry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('beneficiary_name');
            $table->enum('purpose', ['marriage', 'funeral', 'childbirth', 'sickness', 'other'])->default('other');
            $table->date('benefit_date');
            $table->unsignedSmallInteger('benefit_year');
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['ministry_id', 'benefit_year']);
            $table->index(['ministry_id', 'purpose']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministry_welfare_benefits');
    }
};
