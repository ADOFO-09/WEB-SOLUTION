<?php

namespace Database\Seeders;

use App\Models\Ministry;
use App\Models\MinistryGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Demo seeder: seeds the four traditional sub-groups for the Women's Movement ministry.
 * Distributes any existing members of that ministry evenly across the groups.
 *
 * Run manually: php artisan db:seed --class=WomensMinistryGroupsSeeder
 * Guard: only runs outside production.
 */
class WomensMinistryGroupsSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->warn('Skipped: WomensMinistryGroupsSeeder does not run in production.');
            return;
        }

        // Find Women's Movement by type or by name (case-insensitive)
        $ministry = Ministry::where('type', 'women')
            ->orWhere('name', 'like', "%Women%")
            ->first();

        if (!$ministry) {
            $this->command->warn('No Women\'s Ministry found. Create it first, then re-run this seeder.');
            return;
        }

        $adminUserId = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.slug', 'admin')
            ->value('users.id');

        if (!$adminUserId) {
            $adminUserId = DB::table('users')->orderBy('id')->value('id');
        }

        $groupNames = ['Abigail', 'Esther', 'Ruth', 'Deborah'];
        $groups = [];

        foreach ($groupNames as $name) {
            $group = MinistryGroup::firstOrCreate(
                ['ministry_id' => $ministry->id, 'name' => $name],
                [
                    'description' => null,
                    'is_active'   => true,
                    'created_by'  => $adminUserId,
                ]
            );
            $groups[] = $group;
            $this->command->line("  Group: {$name} (id={$group->id})");
        }

        // Distribute existing active members evenly across the 4 groups
        $memberIds = DB::table('member_ministry')
            ->where('ministry_id', $ministry->id)
            ->where('is_active', true)
            ->whereNull('ministry_group_id')
            ->pluck('member_id')
            ->toArray();

        if (empty($memberIds)) {
            $this->command->line('  No unassigned members to distribute.');
        } else {
            $total = count($memberIds);
            foreach ($memberIds as $index => $memberId) {
                $group = $groups[$index % count($groups)];
                DB::table('member_ministry')
                    ->where('ministry_id', $ministry->id)
                    ->where('member_id', $memberId)
                    ->update(['ministry_group_id' => $group->id]);
            }
            $this->command->line("  Distributed {$total} members across " . count($groups) . " groups.");
        }

        $this->command->info("✓ Women's Movement sub-groups seeded for: {$ministry->name}");
    }
}
