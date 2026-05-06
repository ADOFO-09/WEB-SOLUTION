<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Re-encrypt existing plain-text fingerprint templates now that the
        // 'encrypted' cast is in place on the Member model. We read raw DB
        // values to bypass the cast so we can detect plain-text rows safely.
        DB::table('members')
            ->where(function ($q) {
                $q->whereNotNull('fingerprint_template_1')
                  ->orWhereNotNull('fingerprint_template_2');
            })
            ->lazyById()
            ->each(function ($row) {
                $updates = [];

                if ($row->fingerprint_template_1 !== null && !$this->isEncrypted($row->fingerprint_template_1)) {
                    $updates['fingerprint_template_1'] = Crypt::encryptString($row->fingerprint_template_1);
                }

                if ($row->fingerprint_template_2 !== null && !$this->isEncrypted($row->fingerprint_template_2)) {
                    $updates['fingerprint_template_2'] = Crypt::encryptString($row->fingerprint_template_2);
                }

                if (!empty($updates)) {
                    DB::table('members')->where('id', $row->id)->update($updates);
                }
            });
    }

    public function down(): void
    {
        // Decrypt back to plain text to allow rollback
        DB::table('members')
            ->where(function ($q) {
                $q->whereNotNull('fingerprint_template_1')
                  ->orWhereNotNull('fingerprint_template_2');
            })
            ->lazyById()
            ->each(function ($row) {
                $updates = [];

                if ($row->fingerprint_template_1 !== null && $this->isEncrypted($row->fingerprint_template_1)) {
                    $updates['fingerprint_template_1'] = Crypt::decryptString($row->fingerprint_template_1);
                }

                if ($row->fingerprint_template_2 !== null && $this->isEncrypted($row->fingerprint_template_2)) {
                    $updates['fingerprint_template_2'] = Crypt::decryptString($row->fingerprint_template_2);
                }

                if (!empty($updates)) {
                    DB::table('members')->where('id', $row->id)->update($updates);
                }
            });
    }

    private function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
};
