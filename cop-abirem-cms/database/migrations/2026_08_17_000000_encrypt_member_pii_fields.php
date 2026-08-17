<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Fields to encrypt (not used in SQL WHERE/ORDER clauses)
    private array $fields = [
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'phone_secondary',
    ];

    public function up(): void
    {
        // Widen VARCHAR columns that are too small for encrypted ciphertext (~200-300 chars)
        Schema::table('members', function (Blueprint $table) {
            $table->text('phone_secondary')->nullable()->change();
            $table->text('emergency_contact_name')->nullable()->change();
            $table->text('emergency_contact_phone')->nullable()->change();
            // 'address' is already text — no change needed
        });

        // Encrypt existing plain-text values using raw DB queries (bypasses the model cast)
        DB::table('members')->orderBy('id')->each(function ($member) {
            $updates = [];
            foreach ($this->fields as $field) {
                $value = $member->$field;
                if ($value !== null && $value !== '') {
                    $updates[$field] = encrypt($value);
                }
            }
            if (!empty($updates)) {
                DB::table('members')->where('id', $member->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        // Decrypt values back to plain text
        DB::table('members')->orderBy('id')->each(function ($member) {
            $updates = [];
            foreach ($this->fields as $field) {
                $value = $member->$field;
                if ($value !== null && $value !== '') {
                    try {
                        $updates[$field] = decrypt($value);
                    } catch (\Exception) {
                        // Already plain text — leave as-is
                    }
                }
            }
            if (!empty($updates)) {
                DB::table('members')->where('id', $member->id)->update($updates);
            }
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('phone_secondary', 20)->nullable()->change();
            $table->string('emergency_contact_name', 255)->nullable()->change();
            $table->string('emergency_contact_phone', 20)->nullable()->change();
        });
    }
};
