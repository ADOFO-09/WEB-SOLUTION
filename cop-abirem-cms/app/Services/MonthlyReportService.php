<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\MonthlyReport;
use App\Models\Offering;
use App\Models\Tithe;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Compiles a complete auto-populated data array for a monthly report
 * by querying all relevant system data for the given year/month.
 *
 * Fields that have no corresponding system data are left at 0 and
 * remain fully editable by the user.
 */
class MonthlyReportService
{
    // ── Age-group boundaries (years) ──────────────────────────────────────────
    private const AGE_CHILD_MAX        = 12;
    private const AGE_TEEN_MIN         = 13;
    private const AGE_TEEN_MAX         = 19;
    private const AGE_YOUNG_ADULT_MIN  = 20;
    private const AGE_YOUNG_ADULT_MAX  = 35;
    private const AGE_ADULT_MIN        = 36;

    // ── Service-type IDs (from service_types table) ───────────────────────────
    private const ST_SUNDAY    = [1, 2, 9]; // Sunday Worship, Second Service, Thanksgiving
    private const ST_COMMUNION = [8];
    private const ST_MIDWEEK   = [3, 10];   // Midweek Bible Study, Revival
    private const ST_PRAYER    = [4, 5];    // Friday Prayer Meeting, All-Night Prayer

    // ── Expense category IDs considered "human development" ───────────────────
    // 7=Ministry Support, 8=Welfare & Benevolence
    private const EXP_HUMAN_DEV = [7, 8];

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Build the complete auto-populated defaults array for a new report.
     * All values come from live system data; nothing is hard-coded.
     */
    public function prefill(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = Carbon::create($year, $month, 1)->endOfMonth();

        return array_merge(
            $this->leadership(),
            $this->transferIn($start, $end),
            $this->transferOut($start, $end),
            $this->homeCell($start, $end),
            $this->bibleStudy($start, $end),
            $this->soulsWon($start, $end),
            $this->baptisms($start, $end),
            $this->lifeEvents($start, $end),
            $this->worship($start, $end),
            $this->financial($year, $month, $start, $end),
        );
    }

    public function submit(MonthlyReport $report, int $userId): void
    {
        $report->update([
            'status'       => 'submitted',
            'submitted_by' => $userId,
            'submitted_at' => now(),
        ]);
    }

    public function findForMonth(int $year, int $month): ?MonthlyReport
    {
        return MonthlyReport::where('year', $year)->where('month', $month)->first();
    }

    // ── Section 2: Leadership ─────────────────────────────────────────────────

    private function leadership(): array
    {
        $active = Member::where('membership_status', 'active');

        // Leaders = active members with a ministry leadership role (any ministry)
        $leaderIds = DB::table('member_ministry')
            ->whereIn('role', ['leader', 'assistant_leader'])
            ->where('is_active', true)
            ->pluck('member_id');

        return [
            'elders_count'      => (clone $active)->where('title', 'Elder')->count(),
            'deacons_count'     => (clone $active)->where('title', 'Deacon')->count(),
            'deaconesses_count' => (clone $active)->where('title', 'Deaconess')->count(),
            'leaders_count'     => (clone $active)->whereIn('id', $leaderIds)->count(),
        ];
    }

    // ── Section 2: Transfers ──────────────────────────────────────────────────

    /**
     * Transfer In — members whose date_joined falls within the month.
     * Grouped by age group (at time of joining) and gender.
     */
    private function transferIn(Carbon $start, Carbon $end): array
    {
        $members = Member::whereBetween('date_joined', [$start, $end])
            ->whereIn('membership_status', ['active', 'transferred_in'])
            ->select('date_of_birth', 'gender')
            ->get();

        return $this->buildTransferCounts('transfer_in', $members, $start);
    }

    /**
     * Transfer Out — members with membership_status = 'transferred_out'
     * and date_left in the report month.
     */
    private function transferOut(Carbon $start, Carbon $end): array
    {
        $members = Member::whereBetween('date_left', [$start, $end])
            ->where('membership_status', 'transferred_out')
            ->select('date_of_birth', 'gender')
            ->get();

        return $this->buildTransferCounts('transfer_out', $members, $start);
    }

    /** Build the 8 age×gender fields for a transfer direction. */
    private function buildTransferCounts(string $prefix, $members, Carbon $refDate): array
    {
        $out = [
            "{$prefix}_children_male"        => 0,
            "{$prefix}_children_female"      => 0,
            "{$prefix}_teens_male"           => 0,
            "{$prefix}_teens_female"         => 0,
            "{$prefix}_young_adults_male"    => 0,
            "{$prefix}_young_adults_female"  => 0,
            "{$prefix}_adults_male"          => 0,
            "{$prefix}_adults_female"        => 0,
        ];

        foreach ($members as $m) {
            $age    = $m->date_of_birth ? (int) $m->date_of_birth->diffInYears($refDate) : null;
            $gender = $m->gender === 'male' ? 'male' : 'female';
            $group  = $this->ageGroup($age);
            if ($group) {
                $out["{$prefix}_{$group}_{$gender}"]++;
            }
        }

        return $out;
    }

    // ── Section 3: Home Cell ──────────────────────────────────────────────────

    private function homeCell(Carbon $start, Carbon $end): array
    {
        $ids = Ministry::where('type', 'home_cell')->pluck('id');

        // Opened = home_cell ministries created this month
        $opened = Ministry::where('type', 'home_cell')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Closed = home_cell ministries deactivated this month
        $closed = Ministry::where('type', 'home_cell')
            ->where('is_active', false)
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $sessions = AttendanceSession::whereIn('ministry_id', $ids)
            ->whereBetween('service_date', [$start, $end])
            ->pluck('id');

        [$male, $female] = $this->attendanceByGender($sessions);

        return [
            'home_cell_opened'            => $opened,
            'home_cell_closed'            => $closed,
            'home_cell_meetings_held'     => $sessions->count(),
            'home_cell_male_attendance'   => $male,
            'home_cell_female_attendance' => $female,
        ];
    }

    // ── Section 3: Bible Study ────────────────────────────────────────────────

    private function bibleStudy(Carbon $start, Carbon $end): array
    {
        $ids = Ministry::where('type', 'bible_study')->pluck('id');

        $opened = Ministry::where('type', 'bible_study')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $closed = Ministry::where('type', 'bible_study')
            ->where('is_active', false)
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $sessions = AttendanceSession::whereIn('ministry_id', $ids)
            ->whereBetween('service_date', [$start, $end])
            ->pluck('id');

        // Leaders = distinct members with 'leader'/'assistant_leader' role in these ministries
        $leadersCount = DB::table('member_ministry')
            ->whereIn('ministry_id', $ids)
            ->whereIn('role', ['leader', 'assistant_leader'])
            ->where('is_active', true)
            ->distinct('member_id')
            ->count('member_id');

        // Public bible readings = sessions of type Midweek Bible Study within bible_study ministries
        $publicReadings = AttendanceSession::whereIn('ministry_id', $ids)
            ->whereIn('service_type_id', self::ST_MIDWEEK)
            ->whereBetween('service_date', [$start, $end])
            ->count();

        [$male, $female] = $this->attendanceByGender($sessions);

        return [
            'bible_study_opened'           => $opened,
            'bible_study_closed'           => $closed,
            'bible_study_leaders_count'    => $leadersCount,
            'bible_study_meetings_held'    => $sessions->count(),
            'bible_study_male_attendance'  => $male,
            'bible_study_female_attendance'=> $female,
            'bible_study_public_readings'  => $publicReadings,
        ];
    }

    // ── Section 4: Outreaches & Souls Won ────────────────────────────────────

    private function soulsWon(Carbon $start, Carbon $end): array
    {
        // New converts = visitors who were converted to members this month
        $converted = Visitor::whereHas('convertedToMember', function ($q) use ($start, $end) {
            $q->whereBetween('date_joined', [$start, $end]);
        })->with('convertedToMember:id,date_of_birth,gender,date_joined')->get();

        // Gospel Sunday souls = visitors whose first_visit_date was a Sunday this month
        $gospelSunday = Visitor::whereBetween('first_visit_date', [$start, $end])
            ->whereRaw('DAYOFWEEK(first_visit_date) = 1') // 1 = Sunday in MySQL
            ->count();

        // Split converted by age group
        $soulsAdults   = 0;
        $soulsChildren = 0;

        foreach ($converted as $v) {
            $m = $v->convertedToMember;
            if ($m) {
                $age = $m->date_of_birth
                    ? (int) $m->date_of_birth->diffInYears($start)
                    : 18;
                if ($age < self::AGE_TEEN_MIN) {
                    $soulsChildren++;
                } else {
                    $soulsAdults++;
                }
            }
        }

        return [
            'total_outreaches'  => 0, // Not tracked — manual entry
            'souls_adults'      => $soulsAdults,
            'souls_gospel_sunday'    => $gospelSunday,
            'souls_children'    => $soulsChildren,
            'souls_other_cop'   => 0,
            'souls_hum'         => 0,
            'souls_mpwd'        => 0,
            'souls_chaplaincy'  => 0,
            'souls_chieftaincy' => 0,
            'souls_som'         => 0,
            'souls_digital_space'    => 0,
            'backsliders_won_back'       => 0,
            'backsliders_being_followed' => 0,
        ];
    }

    // ── Section 5: Baptisms ───────────────────────────────────────────────────

    private function baptisms(Carbon $start, Carbon $end): array
    {
        // Water baptism: baptism_type IN ('water', 'both')
        $water = Member::whereBetween('baptism_date', [$start, $end])
            ->whereIn('baptism_type', ['water', 'both'])
            ->select('date_of_birth', 'gender', 'baptism_date')
            ->get();

        // Holy Spirit baptism: baptism_type = 'both' (water + HS)
        $hsBoth = Member::whereBetween('baptism_date', [$start, $end])
            ->where('baptism_type', 'both')
            ->count();

        $result = [
            'water_baptism_children_male'       => 0,
            'water_baptism_children_female'     => 0,
            'water_baptism_teens_male'          => 0,
            'water_baptism_teens_female'        => 0,
            'water_baptism_young_adults_male'   => 0,
            'water_baptism_young_adults_female' => 0,
            'water_baptism_adults_male'         => 0,
            'water_baptism_adults_female'       => 0,
            'hs_baptism_new_converts'           => $hsBoth,
            'hs_baptism_old_members'            => 0,
        ];

        foreach ($water as $m) {
            $age    = $m->date_of_birth
                ? (int) $m->date_of_birth->diffInYears(Carbon::parse($m->baptism_date))
                : 25;
            $gender = $m->gender === 'male' ? 'male' : 'female';
            $group  = $this->ageGroup($age);
            if ($group) {
                $result["water_baptism_{$group}_{$gender}"]++;
            }
        }

        return $result;
    }

    // ── Section 6: Life Events ────────────────────────────────────────────────

    private function lifeEvents(Carbon $start, Carbon $end): array
    {
        // Deaths — members with status='deceased' and date_left in the month
        $deceased = Member::whereBetween('date_left', [$start, $end])
            ->where('membership_status', 'deceased')
            ->select('date_of_birth')
            ->get();

        $deathsChildren = 0;
        $deathsAdults   = 0;

        foreach ($deceased as $m) {
            $age = $m->date_of_birth ? $m->date_of_birth->diffInYears($start) : 25;
            if ($age <= self::AGE_CHILD_MAX) {
                $deathsChildren++;
            } else {
                $deathsAdults++;
            }
        }

        return [
            'births'                    => 0, // Not currently tracked — manual
            'male_children_dedicated'   => 0, // Not currently tracked — manual
            'female_children_dedicated' => 0, // Not currently tracked — manual
            'deaths_children'           => $deathsChildren,
            'deaths_adults'             => $deathsAdults,
        ];
    }

    // ── Section 7: Worship & Attendance ──────────────────────────────────────

    private function worship(Carbon $start, Carbon $end): array
    {
        // Sunday morning attendance — sum total_attendance for Sunday service types
        $sundaySessions = AttendanceSession::whereIn('service_type_id', self::ST_SUNDAY)
            ->whereBetween('service_date', [$start, $end])
            ->get();

        $sundayAttendance = $sundaySessions->sum('total_attendance');

        // Communion service
        $communionSessions = AttendanceSession::whereIn('service_type_id', self::ST_COMMUNION)
            ->whereBetween('service_date', [$start, $end])
            ->get();

        $communionAttendance   = $communionSessions->sum('total_attendance');
        $communionParticipants = $communionAttendance; // All attendees at communion service participate

        // Midweek teachings & attendance
        $midweekSessions = AttendanceSession::whereIn('service_type_id', self::ST_MIDWEEK)
            ->whereNull('ministry_id') // exclude bible-study ministry sessions counted in section 3
            ->whereBetween('service_date', [$start, $end])
            ->get();

        // Prayer meetings & attendance
        $prayerSessions = AttendanceSession::whereIn('service_type_id', self::ST_PRAYER)
            ->whereBetween('service_date', [$start, $end])
            ->get();

        // New converts = visitors converted this month
        $newConverts = Visitor::whereHas('convertedToMember', function ($q) use ($start, $end) {
            $q->whereBetween('date_joined', [$start, $end]);
        })->count();

        return [
            'sunday_morning_attendance'     => $sundayAttendance,
            'communion_sunday_attendance'   => $communionAttendance,
            'communion_participants'        => $communionParticipants,
            'new_converts_classes_held'     => 0,   // Not tracked — manual
            'new_converts_class_attendance' => 0,   // Not tracked — manual
            'new_converts_retained'         => $newConverts, // approximate
            'elder_visits_new_converts'     => 0,   // Not tracked — manual
            'midweek_teachings'             => $midweekSessions->count(),
            'midweek_attendance'            => $midweekSessions->sum('total_attendance'),
            'weekly_prayer_meetings'        => $prayerSessions->count(),
            'weekly_prayer_attendance'      => $prayerSessions->sum('total_attendance'),
            'annual_thematic_teachings'     => 0,   // Not tracked — manual
            'holy_ghost_prayer_sessions'    => 0,   // Not tracked — manual
            'marriage_teachings'            => 0,   // Not tracked — manual
            'blessed_marriages'             => 0,   // Not tracked — manual
            'intergenerational_services'    => 0,   // Not tracked — manual
        ];
    }

    // ── Section 9: Financial ──────────────────────────────────────────────────

    private function financial(int $year, int $month, Carbon $start, Carbon $end): array
    {
        // Net tithes for the month
        $netTithes = Tithe::whereYear('month_for', $year)
            ->whereMonth('month_for', $month)
            ->whereNull('voided_at')
            ->sum('amount');

        // Missions offering
        $missionsCategoryIds = IncomeCategory::whereIn('name', [
            'McKeown Missions Offering', 'Mission Offering', 'Missions Offering',
        ])->pluck('id');

        $missionsOffering = Offering::whereIn('income_category_id', $missionsCategoryIds)
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->whereNull('voided_at')
            ->sum('amount');

        // Human development expenses: Welfare & Benevolence, Ministry Support
        $humanExpCategoryIds = DB::table('expense_categories')
            ->whereIn('id', self::EXP_HUMAN_DEV)
            ->pluck('id');

        $humanExp = DB::table('expenses')
            ->whereIn('expense_category_id', $humanExpCategoryIds)
            ->where('status', 'paid')
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        // Non-human expenses = all other paid expenses this month
        $nonHumanExp = DB::table('expenses')
            ->whereNotIn('expense_category_id', $humanExpCategoryIds)
            ->where('status', 'paid')
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        return [
            'monthly_net_tithes'             => round((float) $netTithes, 2),
            'monthly_missions_offering'      => round((float) $missionsOffering, 2),
            'amount_spent_human_development' => round((float) $humanExp, 2),
            'amount_spent_non_human'         => round((float) $nonHumanExp, 2),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Return attendance counts broken down by gender for a set of session IDs.
     * Joins AttendanceRecord → Member to get gender.
     *
     * @param  \Illuminate\Support\Collection $sessionIds
     * @return array{0: int, 1: int}  [male_count, female_count]
     */
    private function attendanceByGender($sessionIds): array
    {
        if ($sessionIds->isEmpty()) {
            return [0, 0];
        }

        $rows = AttendanceRecord::whereIn('session_id', $sessionIds)
            ->whereNotNull('member_id')
            ->join('members', 'attendance_records.member_id', '=', 'members.id')
            ->selectRaw('members.gender, count(*) as cnt')
            ->groupBy('members.gender')
            ->pluck('cnt', 'gender');

        return [
            (int) ($rows['male']   ?? 0),
            (int) ($rows['female'] ?? 0),
        ];
    }

    /**
     * Map an age (integer years) to an age-group slug.
     * Returns null when age is unknown.
     */
    private function ageGroup(?int $age): ?string
    {
        if ($age === null) {
            return 'adults'; // default fallback
        }
        if ($age <= self::AGE_CHILD_MAX) {
            return 'children';
        }
        if ($age <= self::AGE_TEEN_MAX) {
            return 'teens';
        }
        if ($age <= self::AGE_YOUNG_ADULT_MAX) {
            return 'young_adults';
        }
        return 'adults';
    }
}
