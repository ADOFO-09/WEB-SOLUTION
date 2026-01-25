<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone_number', 20);
            $table->string('recipient_name', 255)->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'rejected'])->default('pending');
            $table->string('gateway_message_id', 100)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index('sms_message_id');
            $table->index('status');
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_recipients');
    }
};
