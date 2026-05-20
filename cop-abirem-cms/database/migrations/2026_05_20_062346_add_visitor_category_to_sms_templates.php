<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE sms_templates MODIFY COLUMN category ENUM('general','financial','attendance','event','reminder','birthday','visitor') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sms_templates MODIFY COLUMN category ENUM('general','financial','attendance','event','reminder','birthday') NOT NULL");
    }
};
