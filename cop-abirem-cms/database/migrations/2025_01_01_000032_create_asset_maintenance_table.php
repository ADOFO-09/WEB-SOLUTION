<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->enum('maintenance_type', ['repair', 'service', 'inspection', 'upgrade', 'cleaning']);
            $table->text('description');
            $table->decimal('cost', 15, 2)->default(0.00);
            $table->date('maintenance_date');
            $table->date('next_maintenance_date')->nullable();
            $table->string('performed_by', 255)->nullable();
            $table->string('vendor', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('asset_id');
            $table->index('maintenance_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance');
    }
};
