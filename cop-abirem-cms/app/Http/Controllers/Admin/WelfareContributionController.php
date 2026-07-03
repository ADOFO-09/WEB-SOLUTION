<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\SmsTemplate;
use App\Models\WelfareContribution;
use App\Helpers\SettingHelper;
use App\Services\GiantSmsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class WelfareContributionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:welfare.view', only: ['index', 'show']),
            new Middleware('permission:welfare.create', only: ['create', 'store']),
            new Middleware('permission:welfare.edit', only: ['edit', 'update']),
            new Middleware('permission:welfare.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = WelfareContribution::with(['member', 'receivedBy']);

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('period_month')) {
            $query->where('period_month', $request->period_month);
        }

        if ($request->filled('period_year')) {
            $query->where('period_year', $request->period_year);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('member_id', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderByDesc('payment_date');
        $contributions = $query->paginate(SettingHelper::perPage())->withQueryString();

        $stats = [
            'total_this_year'   => WelfareContribution::where('period_year', date('Y'))->sum('amount'),
            'this_month_count'  => WelfareContribution::where('period_year', date('Y'))->where('period_month', date('n'))->count(),
            'unique_members'    => WelfareContribution::where('period_year', date('Y'))->distinct('member_id')->count('member_id'),
        ];

        $members = Member::active()->orderBy('first_name')->get();

        return view('admin.finance.welfare.contributions.index', compact('contributions', 'stats', 'members'));
    }

    public function create()
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.finance.welfare.contributions.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'amount'         => 'required|numeric|min:0.01',
            'period_month'   => 'required|integer|min:1|max:12',
            'period_year'    => 'required|integer|min:2000|max:2099',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|in:cash,mobile_money,bank_transfer,cheque',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $validated['received_by'] = auth()->user()?->id;

        $contribution = WelfareContribution::create($validated);

        $this->sendContributionSms($contribution);

        return redirect()->route('admin.welfare.contributions.show', $contribution)
            ->with('success', 'Welfare contribution recorded successfully. Ref: ' . $contribution->reference_number);
    }

    public function show(WelfareContribution $welfareContribution)
    {
        $welfareContribution->load(['member', 'receivedBy']);
        return view('admin.finance.welfare.contributions.show', compact('welfareContribution'));
    }

    public function edit(WelfareContribution $welfareContribution)
    {
        $members = Member::active()->orderBy('first_name')->get();
        return view('admin.finance.welfare.contributions.create', compact('welfareContribution', 'members'));
    }

    public function update(Request $request, WelfareContribution $welfareContribution)
    {
        $validated = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'amount'         => 'required|numeric|min:0.01',
            'period_month'   => 'required|integer|min:1|max:12',
            'period_year'    => 'required|integer|min:2000|max:2099',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|in:cash,mobile_money,bank_transfer,cheque',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $welfareContribution->update($validated);

        return redirect()->route('admin.welfare.contributions.show', $welfareContribution)
            ->with('success', 'Welfare contribution updated successfully.');
    }

    public function destroy(WelfareContribution $welfareContribution)
    {
        $welfareContribution->delete();

        return redirect()->route('admin.welfare.contributions.index')
            ->with('success', 'Welfare contribution deleted successfully.');
    }

    private function sendContributionSms(WelfareContribution $contribution): void
    {
        if (!\App\Models\Setting::get('sms_auto_welfare_confirmation', false)) {
            return;
        }

        $member = $contribution->member ?? $contribution->load('member')->member;
        $phone  = $member?->phone_primary;

        if (!$phone) {
            return;
        }

        try {
            $sms = new GiantSmsService();

            if (!$sms->isConfigured()) {
                return;
            }

            $sym    = SettingHelper::currencySymbol();
            $amount = $sym . ' ' . number_format((float) $contribution->amount, 2);
            $period = date('F Y', mktime(0, 0, 0, $contribution->period_month, 1, $contribution->period_year));

            $template = SmsTemplate::where('slug', 'welfare-contribution-confirmation')->where('is_active', true)->first();

            if ($template) {
                $message = $template->renderContent([
                    'member_name'      => $member->first_name,
                    'amount'           => $amount,
                    'period'           => $period,
                    'reference_number' => $contribution->reference_number,
                ]);
            } else {
                $message = 'Dear ' . $member->first_name . ', your welfare due of ' . $amount
                    . ' for ' . $period . ' has been received. Ref: ' . $contribution->reference_number
                    . '. Thank you. God bless you.';
            }

            $sms->send($phone, $message);

        } catch (\Throwable $e) {
            Log::warning('Welfare contribution SMS failed for ref ' . $contribution->reference_number . ': ' . $e->getMessage());
        }
    }
}
