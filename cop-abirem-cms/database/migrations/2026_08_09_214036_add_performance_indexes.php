<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // members: fast enrollment filtering + full-text name search
        Schema::table('members', function (Blueprint $table) {
            $table->index('welfare_enrolled', 'members_welfare_enrolled_idx');
            $table->index('funeral_enrolled', 'members_funeral_enrolled_idx');
            $table->fullText(['first_name', 'last_name'], 'members_name_fulltext');
        });

        // welfare_contributions: composite period lookup + per-member year summaries
        Schema::table('welfare_contributions', function (Blueprint $table) {
            $table->index(['period_year', 'period_month'], 'wc_year_month');
            $table->index(['member_id', 'period_year'], 'wc_member_year');
        });

        // funeral_contributions: same composites
        Schema::table('funeral_contributions', function (Blueprint $table) {
            $table->index(['period_year', 'period_month'], 'fc_year_month');
            $table->index(['member_id', 'period_year'], 'fc_member_year');
        });

        // member_ministry: active-members-per-ministry lookup
        Schema::table('member_ministry', function (Blueprint $table) {
            $table->index(['ministry_id', 'is_active'], 'mm_ministry_active');
        });

        // financial_years: coversToday() date-range query
        Schema::table('financial_years', function (Blueprint $table) {
            $table->index(['start_date', 'end_date'], 'fy_date_range');
        });

        // tithes: per-member financial-year summaries
        Schema::table('tithes', function (Blueprint $table) {
            $table->index(['member_id', 'financial_year_id'], 'tithes_member_fy');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('members_welfare_enrolled_idx');
            $table->dropIndex('members_funeral_enrolled_idx');
            $table->dropFullText('members_name_fulltext');
        });

        Schema::table('welfare_contributions', function (Blueprint $table) {
            $table->dropIndex('wc_year_month');
            $table->dropIndex('wc_member_year');
        });

        Schema::table('funeral_contributions', function (Blueprint $table) {
            $table->dropIndex('fc_year_month');
            $table->dropIndex('fc_member_year');
        });

        Schema::table('member_ministry', function (Blueprint $table) {
            $table->dropIndex('mm_ministry_active');
        });

        Schema::table('financial_years', function (Blueprint $table) {
            $table->dropIndex('fy_date_range');
        });

        Schema::table('tithes', function (Blueprint $table) {
            $table->dropIndex('tithes_member_fy');
        });
    }
};
