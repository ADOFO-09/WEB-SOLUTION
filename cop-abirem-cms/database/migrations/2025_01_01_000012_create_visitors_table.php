<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20);
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->date('first_visit_date');
            $table->enum('referral_source', ['member', 'social_media', 'walk_in', 'event', 'crusade', 'other'])->default('walk_in');
            $table->foreignId('referred_by_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->text('prayer_request')->nullable();
            $table->enum('follow_up_status', ['pending', 'contacted', 'interested', 'not_interested', 'converted'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('converted_to_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('phone');
            $table->index('follow_up_status');
            $table->index('first_visit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
