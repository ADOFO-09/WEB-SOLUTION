<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id', 20)->unique();
            $table->enum('title', ['Mr', 'Mrs', 'Miss', 'Elder', 'Deacon', 'Deaconess', 'Pastor', 'Evangelist', 'Prophet', 'Apostle'])->nullable();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->string('email', 255)->nullable();
            $table->string('phone_primary', 20);
            $table->string('phone_secondary', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('employer', 255)->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->date('date_joined');
            $table->date('baptism_date')->nullable();
            $table->enum('baptism_type', ['water', 'holy_spirit', 'both', 'none'])->default('none');
            $table->enum('membership_status', ['active', 'inactive', 'transferred_out', 'transferred_in', 'deceased'])->default('active');
            $table->string('previous_church', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('member_id');
            $table->index(['first_name', 'last_name']);
            $table->index('phone_primary');
            $table->index('membership_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
