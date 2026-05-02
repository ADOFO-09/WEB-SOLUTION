<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Expense;
use App\Models\LedgerAuditLog;
use App\Models\Offering;
use App\Models\Tithe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LedgerCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $typeFilter = $request->get('type', 'all');

        $logsQuery = LedgerAuditLog::with('performer')->recent(30)->latest();
        if ($typeFilter !== 'all') {
            $logsQuery->where('entry_type', $typeFilter);
        }
        $auditLogs = $logsQuery->paginate(20);

        $voidedTithes    = Tithe::voided()->with('voidedByUser')->latest('voided_at')->get();
        $voidedOfferings = Offering::voided()->with('voidedByUser')->latest('voided_at')->get();
        $voidedDonations = Donation::voided()->with('voidedByUser')->latest('voided_at')->get();
        $voidedExpenses  = Expense::voided()->with('voidedByUser')->latest('voided_at')->get();

        $adjustedTithes    = Tithe::adjusted()->with('adjustmentEntry')->latest()->get();
        $adjustedOfferings = Offering::adjusted()->with('adjustmentEntry')->latest()->get();
        $adjustedDonations = Donation::adjusted()->with('adjustmentEntry')->latest()->get();
        $adjustedExpenses  = Expense::adjusted()->with('adjustmentEntry')->latest()->get();

        return view('admin.finance.corrections.index', compact(
            'auditLogs', 'typeFilter',
            'voidedTithes', 'voidedOfferings', 'voidedDonations', 'voidedExpenses',
            'adjustedTithes', 'adjustedOfferings', 'adjustedDonations', 'adjustedExpenses'
        ));
    }

    // ==========================================
    // VOID
    // ==========================================

    public function voidTithe(Request $request, Tithe $tithe)
    {
        $request->validate(['reason' => 'required|min:10|max:500']);
        if ($tithe->isVoided()) return back()->with('error', 'This entry is already voided.');
        $tithe->voidEntry($request->reason);
        LedgerAuditLog::create([
            'entry_type'   => 'tithe',
            'entry_id'     => $tithe->id,
            'action'       => 'voided',
            'old_values'   => json_encode(['ledger_status' => 'active']),
            'new_values'   => json_encode(['ledger_status' => 'voided', 'void_reason' => $request->reason]),
            'reason'       => $request->reason,
            'performed_by' => auth()->id(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
        return back()->with('success', "Tithe {$tithe->receipt_number} voided successfully.");
    }

    public function voidOffering(Request $request, Offering $offering)
    {
        $request->validate(['reason' => 'required|min:10|max:500']);
        if ($offering->isVoided()) return back()->with('error', 'This entry is already voided.');
        $offering->voidEntry($request->reason);
        LedgerAuditLog::create([
            'entry_type'   => 'offering',
            'entry_id'     => $offering->id,
            'action'       => 'voided',
            'old_values'   => json_encode(['ledger_status' => 'active']),
            'new_values'   => json_encode(['ledger_status' => 'voided', 'void_reason' => $request->reason]),
            'reason'       => $request->reason,
            'performed_by' => auth()->id(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
        return back()->with('success', "Offering {$offering->reference_number} voided successfully.");
    }

    public function voidDonation(Request $request, Donation $donation)
    {
        $request->validate(['reason' => 'required|min:10|max:500']);
        if ($donation->isVoided()) return back()->with('error', 'This entry is already voided.');
        $donation->voidEntry($request->reason);
        LedgerAuditLog::create([
            'entry_type'   => 'donation',
            'entry_id'     => $donation->id,
            'action'       => 'voided',
            'old_values'   => json_encode(['ledger_status' => 'active']),
            'new_values'   => json_encode(['ledger_status' => 'voided', 'void_reason' => $request->reason]),
            'reason'       => $request->reason,
            'performed_by' => auth()->id(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
        return back()->with('success', "Donation {$donation->reference_number} voided successfully.");
    }

    public function voidExpense(Request $request, Expense $expense)
    {
        $request->validate(['reason' => 'required|min:10|max:500']);
        if ($expense->isVoided()) return back()->with('error', 'This entry is already voided.');
        $expense->voidEntry($request->reason);
        LedgerAuditLog::create([
            'entry_type'   => 'expense',
            'entry_id'     => $expense->id,
            'action'       => 'voided',
            'old_values'   => json_encode(['ledger_status' => 'active']),
            'new_values'   => json_encode(['ledger_status' => 'voided', 'void_reason' => $request->reason]),
            'reason'       => $request->reason,
            'performed_by' => auth()->id(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
        return back()->with('success', "Expense {$expense->reference_number} voided successfully.");
    }

    // ==========================================
    // RESTORE
    // ==========================================

    public function restore(Request $request, string $type, int $id)
    {
        $entry = $this->findEntry($type, $id);
        if (!$entry) return back()->with('error', 'Entry not found.');
        if (!$entry->isVoided()) return back()->with('error', 'This entry is not voided.');
        $entry->restoreEntry();
        LedgerAuditLog::create([
            'entry_type'   => $type,
            'entry_id'     => $id,
            'action'       => 'restored',
            'old_values'   => json_encode(['ledger_status' => 'voided']),
            'new_values'   => json_encode(['ledger_status' => 'active']),
            'reason'       => $request->reason ?? null,
            'performed_by' => auth()->id(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
        return back()->with('success', ucfirst($type) . ' entry restored to active successfully.');
    }

    // ==========================================
    // ADJUSTMENT
    // ==========================================

    public function createAdjustment(Request $request, string $type, int $id)
    {
        $entry = $this->findEntry($type, $id);
        if (!$entry) return back()->with('error', 'Entry not found.');
        if (!$entry->isActive()) return back()->with('error', 'Only active entries can be adjusted.');

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'reason'       => 'required|min:10|max:500',
            'payment_date' => 'nullable|date',
        ]);

        $dateField  = $this->getDateField($type);
        $newData    = array_filter([
            'amount'    => $request->amount,
            $dateField  => $request->payment_date ?? $entry->getAttribute($dateField),
        ]);

        $oldAmount = $entry->amount;
        $adjustment = $entry->createAdjustment($newData, $request->reason);
        LedgerAuditLog::create([
            'entry_type'   => $type,
            'entry_id'     => $id,
            'action'       => 'adjusted',
            'old_values'   => json_encode(['amount' => $oldAmount, 'ledger_status' => 'active']),
            'new_values'   => json_encode(['amount' => $request->amount, 'ledger_status' => 'adjusted', 'adjustment_ref' => $adjustment->reference_number]),
            'reason'       => $request->reason,
            'performed_by' => auth()->id(),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);

        return back()->with('success', "Adjustment entry created (Ref: {$adjustment->reference_number}).");
    }

    // ==========================================
    // AUDIT HISTORY
    // ==========================================

    public function auditHistory(string $type, int $id)
    {
        $entry = $this->findEntry($type, $id);
        if (!$entry) abort(404);

        $logs = LedgerAuditLog::with('performer')
            ->forEntry($type, $id)
            ->latest()
            ->get();

        $auditLogs = LedgerAuditLog::where('entry_type', $type)
            ->where('entry_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.finance.corrections.audit-history', compact('entry', 'type', 'id', 'logs', 'auditLogs'));
    }

    // ==========================================
    // PRIVATE
    // ==========================================

    private function findEntry(string $type, int $id): ?Model
    {
        return match($type) {
            'tithe'    => Tithe::find($id),
            'offering' => Offering::find($id),
            'donation' => Donation::find($id),
            'expense'  => Expense::find($id),
            default    => null,
        };
    }

    private function getDateField(string $type): string
    {
        return $type === 'expense' ? 'expense_date' : 'payment_date';
    }
}
