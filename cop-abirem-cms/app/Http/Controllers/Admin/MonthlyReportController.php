<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Services\MonthlyReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyReportController extends Controller
{
    public function __construct(private MonthlyReportService $service) {}

    public function index(): View
    {
        $reports = MonthlyReport::orderByDesc('year')
            ->orderByDesc('month')
            ->with(['creator', 'submitter'])
            ->paginate(24);

        return view('admin.reports.monthly-report.index', compact('reports'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // Prevent duplicate report
        $existing = $this->service->findForMonth($year, $month);
        if ($existing) {
            return redirect()
                ->route('admin.reports.monthly-report.edit', $existing)
                ->with('info', 'A report for ' . Carbon::create($year, $month, 1)->format('F Y') . ' already exists. You can edit it here.');
        }

        $prefill = $this->service->prefill($year, $month);
        $months  = $this->monthOptions();

        return view('admin.reports.monthly-report.form', compact('year', 'month', 'prefill', 'months'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateReport($request);

        // Check duplicate before insert
        $existing = $this->service->findForMonth($validated['year'], $validated['month']);
        if ($existing) {
            return redirect()
                ->route('admin.reports.monthly-report.edit', $existing)
                ->with('info', 'A report for that month already exists.');
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['status']     = $request->has('submit') ? 'submitted' : 'draft';

        if ($validated['status'] === 'submitted') {
            $validated['submitted_by'] = auth()->id();
            $validated['submitted_at'] = now();
        }

        $report = MonthlyReport::create($validated);

        return redirect()
            ->route('admin.reports.monthly-report.show', $report)
            ->with('success', 'Monthly report saved successfully.');
    }

    public function show(MonthlyReport $monthlyReport): View
    {
        $monthlyReport->load(['creator', 'submitter', 'updater']);
        return view('admin.reports.monthly-report.show', ['report' => $monthlyReport]);
    }

    public function edit(MonthlyReport $monthlyReport): View|RedirectResponse
    {
        if ($monthlyReport->status === 'submitted') {
            return redirect()
                ->route('admin.reports.monthly-report.show', $monthlyReport)
                ->with('info', 'Submitted reports cannot be edited.');
        }

        $year    = $monthlyReport->year;
        $month   = $monthlyReport->month;
        $prefill = $monthlyReport->toArray(); // populate form from saved data
        $months  = $this->monthOptions();

        return view('admin.reports.monthly-report.form', compact('year', 'month', 'prefill', 'months', 'monthlyReport'));
    }

    public function update(Request $request, MonthlyReport $monthlyReport): RedirectResponse
    {
        if ($monthlyReport->status === 'submitted') {
            return back()->with('error', 'Submitted reports cannot be edited.');
        }

        $validated = $this->validateReport($request);
        $validated['updated_by'] = auth()->id();

        if ($request->has('submit')) {
            $validated['status']       = 'submitted';
            $validated['submitted_by'] = auth()->id();
            $validated['submitted_at'] = now();
        }

        $monthlyReport->update($validated);

        return redirect()
            ->route('admin.reports.monthly-report.show', $monthlyReport)
            ->with('success', 'Monthly report updated successfully.');
    }

    public function print(MonthlyReport $monthlyReport): View
    {
        return view('admin.reports.monthly-report.print', ['report' => $monthlyReport]);
    }

    public function pdf(MonthlyReport $monthlyReport)
    {
        $pdf = Pdf::loadView('admin.reports.monthly-report.pdf', ['report' => $monthlyReport])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'Arial',
                'margin_top'           => 6,
                'margin_right'         => 8,
                'margin_bottom'        => 6,
                'margin_left'          => 8,
            ]);

        $filename = 'Monthly_Report_' . $monthlyReport->year . '_' . str_pad($monthlyReport->month, 2, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function validateReport(Request $request): array
    {
        return $request->validate([
            'year'  => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',

            // Leadership
            'elders_count'      => 'nullable|integer|min:0',
            'deacons_count'     => 'nullable|integer|min:0',
            'deaconesses_count' => 'nullable|integer|min:0',
            'leaders_count'     => 'nullable|integer|min:0',

            // Transfer In
            'transfer_in_children_male'        => 'nullable|integer|min:0',
            'transfer_in_children_female'      => 'nullable|integer|min:0',
            'transfer_in_teens_male'           => 'nullable|integer|min:0',
            'transfer_in_teens_female'         => 'nullable|integer|min:0',
            'transfer_in_young_adults_male'    => 'nullable|integer|min:0',
            'transfer_in_young_adults_female'  => 'nullable|integer|min:0',
            'transfer_in_adults_male'          => 'nullable|integer|min:0',
            'transfer_in_adults_female'        => 'nullable|integer|min:0',

            // Transfer Out
            'transfer_out_children_male'       => 'nullable|integer|min:0',
            'transfer_out_children_female'     => 'nullable|integer|min:0',
            'transfer_out_teens_male'          => 'nullable|integer|min:0',
            'transfer_out_teens_female'        => 'nullable|integer|min:0',
            'transfer_out_young_adults_male'   => 'nullable|integer|min:0',
            'transfer_out_young_adults_female' => 'nullable|integer|min:0',
            'transfer_out_adults_male'         => 'nullable|integer|min:0',
            'transfer_out_adults_female'       => 'nullable|integer|min:0',

            // Home Cell
            'home_cell_opened'           => 'nullable|integer|min:0',
            'home_cell_closed'           => 'nullable|integer|min:0',
            'home_cell_meetings_held'    => 'nullable|integer|min:0',
            'home_cell_male_attendance'  => 'nullable|integer|min:0',
            'home_cell_female_attendance'=> 'nullable|integer|min:0',

            // Bible Study
            'bible_study_opened'          => 'nullable|integer|min:0',
            'bible_study_closed'          => 'nullable|integer|min:0',
            'bible_study_leaders_count'   => 'nullable|integer|min:0',
            'bible_study_meetings_held'   => 'nullable|integer|min:0',
            'bible_study_male_attendance' => 'nullable|integer|min:0',
            'bible_study_female_attendance'=> 'nullable|integer|min:0',
            'bible_study_public_readings' => 'nullable|integer|min:0',

            // Outreaches
            'total_outreaches'           => 'nullable|integer|min:0',
            'souls_adults'               => 'nullable|integer|min:0',
            'souls_gospel_sunday'        => 'nullable|integer|min:0',
            'souls_children'             => 'nullable|integer|min:0',
            'souls_other_cop'            => 'nullable|integer|min:0',
            'souls_hum'                  => 'nullable|integer|min:0',
            'souls_mpwd'                 => 'nullable|integer|min:0',
            'souls_chaplaincy'           => 'nullable|integer|min:0',
            'souls_chieftaincy'          => 'nullable|integer|min:0',
            'souls_som'                  => 'nullable|integer|min:0',
            'souls_digital_space'        => 'nullable|integer|min:0',
            'backsliders_won_back'       => 'nullable|integer|min:0',
            'backsliders_being_followed' => 'nullable|integer|min:0',

            // Baptisms
            'water_baptism_children_male'        => 'nullable|integer|min:0',
            'water_baptism_children_female'      => 'nullable|integer|min:0',
            'water_baptism_teens_male'           => 'nullable|integer|min:0',
            'water_baptism_teens_female'         => 'nullable|integer|min:0',
            'water_baptism_young_adults_male'    => 'nullable|integer|min:0',
            'water_baptism_young_adults_female'  => 'nullable|integer|min:0',
            'water_baptism_adults_male'          => 'nullable|integer|min:0',
            'water_baptism_adults_female'        => 'nullable|integer|min:0',
            'hs_baptism_new_converts'            => 'nullable|integer|min:0',
            'hs_baptism_old_members'             => 'nullable|integer|min:0',

            // Life Events
            'births'                   => 'nullable|integer|min:0',
            'male_children_dedicated'  => 'nullable|integer|min:0',
            'female_children_dedicated'=> 'nullable|integer|min:0',
            'deaths_children'          => 'nullable|integer|min:0',
            'deaths_adults'            => 'nullable|integer|min:0',

            // Worship & Attendance
            'sunday_morning_attendance'    => 'nullable|integer|min:0',
            'communion_sunday_attendance'  => 'nullable|integer|min:0',
            'communion_participants'       => 'nullable|integer|min:0',
            'new_converts_classes_held'    => 'nullable|integer|min:0',
            'new_converts_class_attendance'=> 'nullable|integer|min:0',
            'new_converts_retained'        => 'nullable|integer|min:0',
            'elder_visits_new_converts'    => 'nullable|integer|min:0',
            'midweek_teachings'            => 'nullable|integer|min:0',
            'midweek_attendance'           => 'nullable|integer|min:0',
            'weekly_prayer_meetings'       => 'nullable|integer|min:0',
            'weekly_prayer_attendance'     => 'nullable|integer|min:0',
            'annual_thematic_teachings'    => 'nullable|integer|min:0',
            'holy_ghost_prayer_sessions'   => 'nullable|integer|min:0',
            'marriage_teachings'           => 'nullable|integer|min:0',
            'blessed_marriages'            => 'nullable|integer|min:0',
            'intergenerational_services'   => 'nullable|integer|min:0',

            // Social
            'social_tertiary_sponsorship'    => 'nullable|integer|min:0',
            'social_pre_tertiary_sponsorship'=> 'nullable|integer|min:0',
            'social_health_support'          => 'nullable|integer|min:0',
            'social_apprenticeship_support'  => 'nullable|integer|min:0',
            'social_community_transformation'=> 'nullable|integer|min:0',
            'social_environmental_care'      => 'nullable|integer|min:0',
            'beneficiaries_male'             => 'nullable|integer|min:0',
            'beneficiaries_female'           => 'nullable|integer|min:0',
            'beneficiaries_other'            => 'nullable|integer|min:0',

            // Financial
            'amount_spent_human_development' => 'nullable|numeric|min:0',
            'amount_spent_non_human'         => 'nullable|numeric|min:0',
            'monthly_net_tithes'             => 'nullable|numeric|min:0',
            'monthly_missions_offering'      => 'nullable|numeric|min:0',

            'notes' => 'nullable|string|max:2000',
        ]);
    }

    private function monthOptions(): array
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create(null, $i, 1)->format('F');
        }
        return $months;
    }
}
