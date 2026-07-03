<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('funeral_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('benefactor_name', 255);
            $table->string('deceased_name', 255)->nullable();
            $table->date('funeral_date');
            $table->smallInteger('funeral_year')->unsigned();
            $table->string('venue', 255)->nullable();
            $table->decimal('amount_donated', 10, 2);
            $table->text('description')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('funeral_year');
            $table->index('funeral_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funeral_benefits');
    }
};
