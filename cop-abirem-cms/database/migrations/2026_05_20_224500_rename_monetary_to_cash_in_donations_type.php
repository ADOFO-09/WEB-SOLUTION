<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand ENUM to include both old and new value so the UPDATE is valid
        DB::statement("ALTER TABLE donations MODIFY COLUMN donation_type ENUM('monetary','in_kind','cash') NOT NULL DEFAULT 'monetary'");
        // Step 2: migrate existing data
        DB::statement("UPDATE donations SET donation_type = 'cash' WHERE donation_type = 'monetary'");
        // Step 3: remove the old value
        DB::statement("ALTER TABLE donations MODIFY COLUMN donation_type ENUM('cash','in_kind') NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        DB::statement("UPDATE donations SET donation_type = 'monetary' WHERE donation_type = 'cash'");
        DB::statement("ALTER TABLE donations MODIFY COLUMN donation_type ENUM('monetary','in_kind') NOT NULL DEFAULT 'monetary'");
    }
};
