<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('welfare_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('benefactor_name', 255);
            $table->enum('purpose', ['marriage', 'funeral', 'childbirth', 'other']);
            $table->date('benefit_date');
            $table->smallInteger('benefit_year')->unsigned();
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('benefit_year');
            $table->index('benefit_date');
            $table->index('purpose');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_benefits');
    }
};
