<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('message_type', ['bulk', 'individual', 'automated'])->default('bulk');
            $table->enum('category', ['general', 'financial', 'attendance', 'event', 'reminder', 'birthday'])->default('general');
            $table->string('subject', 255)->nullable();
            $table->text('message_content');
            $table->integer('recipient_count')->default(0);
            $table->integer('successful_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'partially_sent', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('sent_by')->constrained('users');
            $table->timestamps();
            
            $table->index('status');
            $table->index('message_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
