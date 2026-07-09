<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncGenderMinistries extends Command
{
    protected $signature   = 'members:sync-gender-ministries {--dry-run : Preview without making changes}';
    protected $description = "Enroll existing members into their gender-matched ministry (Men's or Women's) based on the ministry type column.";

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // Resolve ministries by type column — no hardcoded names or IDs
        $menMinistry   = DB::table('ministries')->where('type', 'men')->where('is_active', true)->first();
        $womenMinistry = DB::table('ministries')->where('type', 'women')->where('is_active', true)->first();

        if (!$menMinistry) {
            $this->error("No active ministry with type='men' found.");
            return self::FAILURE;
        }
        if (!$womenMinistry) {
            $this->error("No active ministry with type='women' found.");
            return self::FAILURE;
        }

        $this->line("Men's ministry   : [{$menMinistry->id}] {$menMinistry->name}");
        $this->line("Women's ministry : [{$womenMinistry->id}] {$womenMinistry->name}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('-- DRY RUN: no changes will be written --');
            $this->newLine();
        }

        $enrolled    = 0;
        $reactivated = 0;
        $skipped     = 0;

        foreach ([
            ['gender' => 'male',   'ministry' => $menMinistry],
            ['gender' => 'female', 'ministry' => $womenMinistry],
        ] as $rule) {
            $gender   = $rule['gender'];
            $ministry = $rule['ministry'];

            $memberIds = DB::table('members')
                ->where('gender', $gender)
                ->whereNull('deleted_at')
                ->pluck('id');

            $this->info("Processing {$memberIds->count()} {$gender} member(s) → [{$ministry->name}]");

            foreach ($memberIds as $memberId) {
                $pivot = DB::table('member_ministry')
                    ->where('member_id', $memberId)
                    ->where('ministry_id', $ministry->id)
                    ->first();

                if ($pivot) {
                    if ($pivot->is_active) {
                        $skipped++;
                    } else {
                        if (!$dryRun) {
                            DB::table('member_ministry')
                                ->where('member_id', $memberId)
                                ->where('ministry_id', $ministry->id)
                                ->update([
                                    'is_active'   => true,
                                    'left_date'   => null,
                                    'joined_date' => now()->toDateString(),
                                    'updated_at'  => now(),
                                ]);
                        }
                        $reactivated++;
                        $this->line("  <fg=yellow>REACTIVATED</> member #{$memberId}");
                    }
                } else {
                    if (!$dryRun) {
                        DB::table('member_ministry')->insert([
                            'member_id'   => $memberId,
                            'ministry_id' => $ministry->id,
                            'role'        => 'member',
                            'joined_date' => now()->toDateString(),
                            'is_active'   => true,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
                    $enrolled++;
                    $this->line("  <fg=green>ENROLLED</>  member #{$memberId}");
                }
            }

            $this->newLine();
        }

        $prefix = $dryRun ? '[DRY RUN] Would have' : 'Done.';
        $this->info("{$prefix} enrolled {$enrolled} new, reactivated {$reactivated}, skipped {$skipped} already-active.");

        return self::SUCCESS;
    }
}
