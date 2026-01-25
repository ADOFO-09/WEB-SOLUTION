<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 50)->unique();
            $table->string('name', 255);
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->text('description')->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('current_value', 15, 2);
            $table->string('supplier', 255)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('location', 255)->nullable();
            $table->foreignId('assigned_to_ministry_id')->nullable()->constrained('ministries')->nullOnDelete();
            $table->enum('condition_status', ['excellent', 'good', 'fair', 'poor', 'damaged', 'unusable'])->default('good');
            $table->enum('status', ['active', 'maintenance', 'disposed', 'lost', 'stolen'])->default('active');
            $table->date('disposal_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('asset_code');
            $table->index('status');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
