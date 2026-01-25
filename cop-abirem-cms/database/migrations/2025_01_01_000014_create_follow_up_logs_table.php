<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('contacted_by')->constrained('users')->onDelete('cascade');
            $table->date('contact_date');
            $table->enum('contact_method', ['phone', 'sms', 'visit', 'email', 'whatsapp']);
            $table->enum('outcome', ['reached', 'no_answer', 'callback', 'not_interested', 'interested']);
            $table->text('notes')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('contact_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_logs');
    }
};
