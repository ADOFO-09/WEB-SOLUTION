<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyReport extends Model
{
    protected $fillable = [
        'year', 'month', 'status',

        // Section 2: Leadership
        'elders_count', 'deacons_count', 'deaconesses_count', 'leaders_count',

        // Transfer In
        'transfer_in_children_male', 'transfer_in_children_female',
        'transfer_in_teens_male', 'transfer_in_teens_female',
        'transfer_in_young_adults_male', 'transfer_in_young_adults_female',
        'transfer_in_adults_male', 'transfer_in_adults_female',

        // Transfer Out
        'transfer_out_children_male', 'transfer_out_children_female',
        'transfer_out_teens_male', 'transfer_out_teens_female',
        'transfer_out_young_adults_male', 'transfer_out_young_adults_female',
        'transfer_out_adults_male', 'transfer_out_adults_female',

        // Section 3: Home Cell
        'home_cell_opened', 'home_cell_closed', 'home_cell_meetings_held',
        'home_cell_male_attendance', 'home_cell_female_attendance',

        // Bible Study
        'bible_study_opened', 'bible_study_closed', 'bible_study_leaders_count',
        'bible_study_meetings_held', 'bible_study_male_attendance',
        'bible_study_female_attendance', 'bible_study_public_readings',

        // Section 4: Outreaches & Souls Won
        'total_outreaches', 'souls_adults', 'souls_gospel_sunday', 'souls_children',
        'souls_other_cop', 'souls_hum', 'souls_mpwd', 'souls_chaplaincy',
        'souls_chieftaincy', 'souls_som', 'souls_digital_space',
        'backsliders_won_back', 'backsliders_being_followed',

        // Section 5: Baptisms
        'water_baptism_children_male', 'water_baptism_children_female',
        'water_baptism_teens_male', 'water_baptism_teens_female',
        'water_baptism_young_adults_male', 'water_baptism_young_adults_female',
        'water_baptism_adults_male', 'water_baptism_adults_female',
        'hs_baptism_new_converts', 'hs_baptism_old_members',

        // Section 6: Life Events
        'births', 'male_children_dedicated', 'female_children_dedicated',
        'deaths_children', 'deaths_adults',

        // Section 7: Worship & Attendance
        'sunday_morning_attendance', 'communion_sunday_attendance', 'communion_participants',
        'new_converts_classes_held', 'new_converts_class_attendance', 'new_converts_retained',
        'elder_visits_new_converts', 'midweek_teachings', 'midweek_attendance',
        'weekly_prayer_meetings', 'weekly_prayer_attendance',
        'annual_thematic_teachings', 'holy_ghost_prayer_sessions',
        'marriage_teachings', 'blessed_marriages', 'intergenerational_services',

        // Section 8: Social Interventions
        'social_tertiary_sponsorship', 'social_pre_tertiary_sponsorship',
        'social_health_support', 'social_apprenticeship_support',
        'social_community_transformation', 'social_environmental_care',
        'beneficiaries_male', 'beneficiaries_female', 'beneficiaries_other',

        // Section 9: Financial
        'amount_spent_human_development', 'amount_spent_non_human',
        'monthly_net_tithes', 'monthly_missions_offering',

        'notes', 'created_by', 'updated_by', 'submitted_by', 'submitted_at',
    ];

    protected $casts = [
        'year'  => 'integer',
        'month' => 'integer',
        'submitted_at' => 'datetime',

        'amount_spent_human_development' => 'decimal:2',
        'amount_spent_non_human'         => 'decimal:2',
        'monthly_net_tithes'             => 'decimal:2',
        'monthly_missions_offering'      => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // ── Computed Totals ────────────────────────────────────────────────────────

    public function getMonthNameAttribute(): string
    {
        return \Carbon\Carbon::create($this->year, $this->month, 1)->format('F');
    }

    public function getPeriodLabelAttribute(): string
    {
        return \Carbon\Carbon::create($this->year, $this->month, 1)->format('F Y');
    }

    public function getTotalTransferInAttribute(): int
    {
        return $this->transfer_in_children_male + $this->transfer_in_children_female
            + $this->transfer_in_teens_male + $this->transfer_in_teens_female
            + $this->transfer_in_young_adults_male + $this->transfer_in_young_adults_female
            + $this->transfer_in_adults_male + $this->transfer_in_adults_female;
    }

    public function getTotalTransferOutAttribute(): int
    {
        return $this->transfer_out_children_male + $this->transfer_out_children_female
            + $this->transfer_out_teens_male + $this->transfer_out_teens_female
            + $this->transfer_out_young_adults_male + $this->transfer_out_young_adults_female
            + $this->transfer_out_adults_male + $this->transfer_out_adults_female;
    }

    public function getTotalHomeCellAttendanceAttribute(): int
    {
        return $this->home_cell_male_attendance + $this->home_cell_female_attendance;
    }

    public function getTotalBibleStudyAttendanceAttribute(): int
    {
        return $this->bible_study_male_attendance + $this->bible_study_female_attendance;
    }

    public function getTotalSoulsWonAttribute(): int
    {
        return $this->souls_adults + $this->souls_gospel_sunday + $this->souls_children
            + $this->souls_other_cop + $this->souls_hum + $this->souls_mpwd
            + $this->souls_chaplaincy + $this->souls_chieftaincy
            + $this->souls_som + $this->souls_digital_space;
    }

    public function getTotalWaterBaptismsAttribute(): int
    {
        return $this->water_baptism_children_male + $this->water_baptism_children_female
            + $this->water_baptism_teens_male + $this->water_baptism_teens_female
            + $this->water_baptism_young_adults_male + $this->water_baptism_young_adults_female
            + $this->water_baptism_adults_male + $this->water_baptism_adults_female;
    }

    public function getTotalHsBaptismsAttribute(): int
    {
        return $this->hs_baptism_new_converts + $this->hs_baptism_old_members;
    }

    public function getTotalChildrenDedicatedAttribute(): int
    {
        return $this->male_children_dedicated + $this->female_children_dedicated;
    }

    public function getTotalDeathsAttribute(): int
    {
        return $this->deaths_children + $this->deaths_adults;
    }

    public function getTotalBeneficiariesAttribute(): int
    {
        return $this->beneficiaries_male + $this->beneficiaries_female + $this->beneficiaries_other;
    }

    public function getTotalAmountSpentAttribute(): float
    {
        return (float) $this->amount_spent_human_development + (float) $this->amount_spent_non_human;
    }
}
