<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month'); // 1–12
            $table->enum('status', ['draft', 'submitted'])->default('draft');

            // ── Section 2: Leadership ──────────────────────────────────
            $table->unsignedSmallInteger('elders_count')->default(0);
            $table->unsignedSmallInteger('deacons_count')->default(0);
            $table->unsignedSmallInteger('deaconesses_count')->default(0);
            $table->unsignedSmallInteger('leaders_count')->default(0);

            // Transfer In
            $table->unsignedSmallInteger('transfer_in_children_male')->default(0);
            $table->unsignedSmallInteger('transfer_in_children_female')->default(0);
            $table->unsignedSmallInteger('transfer_in_teens_male')->default(0);
            $table->unsignedSmallInteger('transfer_in_teens_female')->default(0);
            $table->unsignedSmallInteger('transfer_in_young_adults_male')->default(0);
            $table->unsignedSmallInteger('transfer_in_young_adults_female')->default(0);
            $table->unsignedSmallInteger('transfer_in_adults_male')->default(0);
            $table->unsignedSmallInteger('transfer_in_adults_female')->default(0);

            // Transfer Out
            $table->unsignedSmallInteger('transfer_out_children_male')->default(0);
            $table->unsignedSmallInteger('transfer_out_children_female')->default(0);
            $table->unsignedSmallInteger('transfer_out_teens_male')->default(0);
            $table->unsignedSmallInteger('transfer_out_teens_female')->default(0);
            $table->unsignedSmallInteger('transfer_out_young_adults_male')->default(0);
            $table->unsignedSmallInteger('transfer_out_young_adults_female')->default(0);
            $table->unsignedSmallInteger('transfer_out_adults_male')->default(0);
            $table->unsignedSmallInteger('transfer_out_adults_female')->default(0);

            // ── Section 3: Home Cell ──────────────────────────────────
            $table->unsignedSmallInteger('home_cell_opened')->default(0);
            $table->unsignedSmallInteger('home_cell_closed')->default(0);
            $table->unsignedSmallInteger('home_cell_meetings_held')->default(0);
            $table->unsignedSmallInteger('home_cell_male_attendance')->default(0);
            $table->unsignedSmallInteger('home_cell_female_attendance')->default(0);

            // Bible Study Groups
            $table->unsignedSmallInteger('bible_study_opened')->default(0);
            $table->unsignedSmallInteger('bible_study_closed')->default(0);
            $table->unsignedSmallInteger('bible_study_leaders_count')->default(0);
            $table->unsignedSmallInteger('bible_study_meetings_held')->default(0);
            $table->unsignedSmallInteger('bible_study_male_attendance')->default(0);
            $table->unsignedSmallInteger('bible_study_female_attendance')->default(0);
            $table->unsignedSmallInteger('bible_study_public_readings')->default(0);

            // ── Section 4: Outreaches & Souls Won ────────────────────
            $table->unsignedSmallInteger('total_outreaches')->default(0);
            $table->unsignedSmallInteger('souls_adults')->default(0);
            $table->unsignedSmallInteger('souls_gospel_sunday')->default(0);
            $table->unsignedSmallInteger('souls_children')->default(0);
            $table->unsignedSmallInteger('souls_other_cop')->default(0);
            $table->unsignedSmallInteger('souls_hum')->default(0);
            $table->unsignedSmallInteger('souls_mpwd')->default(0);
            $table->unsignedSmallInteger('souls_chaplaincy')->default(0);
            $table->unsignedSmallInteger('souls_chieftaincy')->default(0);
            $table->unsignedSmallInteger('souls_som')->default(0);
            $table->unsignedSmallInteger('souls_digital_space')->default(0);
            $table->unsignedSmallInteger('backsliders_won_back')->default(0);
            $table->unsignedSmallInteger('backsliders_being_followed')->default(0);

            // ── Section 5: Baptisms ───────────────────────────────────
            $table->unsignedSmallInteger('water_baptism_children_male')->default(0);
            $table->unsignedSmallInteger('water_baptism_children_female')->default(0);
            $table->unsignedSmallInteger('water_baptism_teens_male')->default(0);
            $table->unsignedSmallInteger('water_baptism_teens_female')->default(0);
            $table->unsignedSmallInteger('water_baptism_young_adults_male')->default(0);
            $table->unsignedSmallInteger('water_baptism_young_adults_female')->default(0);
            $table->unsignedSmallInteger('water_baptism_adults_male')->default(0);
            $table->unsignedSmallInteger('water_baptism_adults_female')->default(0);
            $table->unsignedSmallInteger('hs_baptism_new_converts')->default(0);
            $table->unsignedSmallInteger('hs_baptism_old_members')->default(0);

            // ── Section 6: Life Events ────────────────────────────────
            $table->unsignedSmallInteger('births')->default(0);
            $table->unsignedSmallInteger('male_children_dedicated')->default(0);
            $table->unsignedSmallInteger('female_children_dedicated')->default(0);
            $table->unsignedSmallInteger('deaths_children')->default(0);
            $table->unsignedSmallInteger('deaths_adults')->default(0);

            // ── Section 7: Worship & Attendance ──────────────────────
            $table->unsignedSmallInteger('sunday_morning_attendance')->default(0);
            $table->unsignedSmallInteger('communion_sunday_attendance')->default(0);
            $table->unsignedSmallInteger('communion_participants')->default(0);
            $table->unsignedSmallInteger('new_converts_classes_held')->default(0);
            $table->unsignedSmallInteger('new_converts_class_attendance')->default(0);
            $table->unsignedSmallInteger('new_converts_retained')->default(0);
            $table->unsignedSmallInteger('elder_visits_new_converts')->default(0);
            $table->unsignedSmallInteger('midweek_teachings')->default(0);
            $table->unsignedSmallInteger('midweek_attendance')->default(0);
            $table->unsignedSmallInteger('weekly_prayer_meetings')->default(0);
            $table->unsignedSmallInteger('weekly_prayer_attendance')->default(0);
            $table->unsignedSmallInteger('annual_thematic_teachings')->default(0);
            $table->unsignedSmallInteger('holy_ghost_prayer_sessions')->default(0);
            $table->unsignedSmallInteger('marriage_teachings')->default(0);
            $table->unsignedSmallInteger('blessed_marriages')->default(0);
            $table->unsignedSmallInteger('intergenerational_services')->default(0);

            // ── Section 8: Social Interventions ──────────────────────
            $table->unsignedSmallInteger('social_tertiary_sponsorship')->default(0);
            $table->unsignedSmallInteger('social_pre_tertiary_sponsorship')->default(0);
            $table->unsignedSmallInteger('social_health_support')->default(0);
            $table->unsignedSmallInteger('social_apprenticeship_support')->default(0);
            $table->unsignedSmallInteger('social_community_transformation')->default(0);
            $table->unsignedSmallInteger('social_environmental_care')->default(0);
            $table->unsignedSmallInteger('beneficiaries_male')->default(0);
            $table->unsignedSmallInteger('beneficiaries_female')->default(0);
            $table->unsignedSmallInteger('beneficiaries_other')->default(0);

            // ── Section 9: Financial ──────────────────────────────────
            $table->decimal('amount_spent_human_development', 12, 2)->default(0);
            $table->decimal('amount_spent_non_human', 12, 2)->default(0);
            $table->decimal('monthly_net_tithes', 12, 2)->default(0);
            $table->decimal('monthly_missions_offering', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('submitted_by')->nullable()->constrained('users');
            $table->timestamp('submitted_at')->nullable();

            $table->unique(['year', 'month']); // one report per month
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
