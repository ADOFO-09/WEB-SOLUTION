<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->date('visit_date');
            $table->foreignId('service_type_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('visit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_visits');
    }
};
