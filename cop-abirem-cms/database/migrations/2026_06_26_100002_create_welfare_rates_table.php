<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('welfare_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->date('effective_from');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index('effective_from');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_rates');
    }
};
