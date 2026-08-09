@extends('layouts.admin')

@section('title', 'Record Welfare Benefit')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Record Welfare Benefit</h1>
        <p style="color:#64748b;margin-top:0.25rem;">{{ $ministry->name }}</p>
    </div>
    <a href="{{ route('admin.ministry.finance.welfare.benefits') }}" style="font-size:0.85rem;color:#64748b;">&larr; Back to Benefits</a>
</div>

<div style="max-width:780px;">
    <div style="background:#fff;border-radius:12px;padding:2rem;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <form method="POST" action="{{ route('admin.ministry.finance.welfare.benefits.store') }}" id="benefitForm">
            @csrf

            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;">
                <ul style="margin:0;padding-left:1.25rem;">
                    @foreach($errors->all() as $error)
                    <li style="font-size:0.85rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="display:grid;gap:1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="font-size:0.82rem;font-weight:500;color:#374151;">Beneficiary Name <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="beneficiary_name" value="{{ old('beneficiary_name') }}" required
                               style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="font-size:0.82rem;font-weight:500;color:#374151;">Ministry Member (optional)</label>
                        <select name="member_id" style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                            <option value="">Not a member / External</option>
                            @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->first_name }} {{ $member->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="font-size:0.82rem;font-weight:500;color:#374151;">Purpose <span style="color:#dc2626;">*</span></label>
                        <select name="purpose" required style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;">
                            <option value="">Select purpose...</option>
                            @foreach($purposes as $key => $label)
                            <option value="{{ $key }}" {{ old('purpose') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.82rem;font-weight:500;color:#374151;">Benefit Date <span style="color:#dc2626;">*</span></label>
                        <input type="date" name="benefit_date" value="{{ old('benefit_date', now()->toDateString()) }}" required
                               style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;box-sizing:border-box;">
                    </div>
                </div>

                <div>
                    <label style="font-size:0.82rem;font-weight:500;color:#374151;">Amount Disbursed ({{ $currencySymbol }}) <span style="color:#dc2626;">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', 0) }}" step="0.01" min="0" required
                           style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;box-sizing:border-box;">
                    <p style="font-size:0.72rem;color:#94a3b8;margin-top:0.2rem;">Direct cash/cheque disbursed. Use 0 if everything is captured in expense items below.</p>
                </div>

                <div>
                    <label style="font-size:0.82rem;font-weight:500;color:#374151;">Description / Notes</label>
                    <textarea name="description" rows="2"
                              style="width:100%;padding:0.5rem 0.75rem;border:1px solid #d1d5db;border-radius:6px;font-size:0.875rem;margin-top:0.25rem;resize:vertical;box-sizing:border-box;">{{ old('description') }}</textarea>
                </div>

                <!-- Expense line items -->
                <div style="border:1px solid #e2e8f0;border-radius:8px;padding:1rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                        <label style="font-size:0.85rem;font-weight:600;color:#374151;">Expense Breakdown (optional)</label>
                        <button type="button" id="addExpenseRow"
                                style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:5px;padding:0.25rem 0.7rem;font-size:0.78rem;cursor:pointer;">
                            + Add Item
                        </button>
                    </div>
                    <div id="expenseRows">
                        <!-- Rows added by JS -->
                    </div>
                    <p style="font-size:0.72rem;color:#94a3b8;margin-top:0.5rem;">Break down how the welfare was spent (e.g. food items, transport, medical bills).</p>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
                <button type="submit" style="background:#1e3a5f;color:#fff;padding:0.65rem 1.5rem;border-radius:8px;border:none;font-size:0.9rem;font-weight:600;cursor:pointer;">
                    Save Benefit
                </button>
                <a href="{{ route('admin.ministry.finance.welfare.benefits') }}"
                   style="background:#f1f5f9;color:#475569;padding:0.65rem 1.25rem;border-radius:8px;font-size:0.9rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let expenseIndex = 0;
function addExpenseRow() {
    const container = document.getElementById('expenseRows');
    const row = document.createElement('div');
    row.style = 'display:grid;grid-template-columns:1fr auto auto;gap:0.5rem;margin-bottom:0.5rem;align-items:center;';
    row.innerHTML = `
        <input type="text" name="expenses[${expenseIndex}][description]" placeholder="Description (e.g. Food items)"
               style="padding:0.4rem 0.65rem;border:1px solid #d1d5db;border-radius:5px;font-size:0.82rem;">
        <input type="number" name="expenses[${expenseIndex}][amount]" placeholder="Amount" step="0.01" min="0.01"
               style="width:110px;padding:0.4rem 0.65rem;border:1px solid #d1d5db;border-radius:5px;font-size:0.82rem;">
        <button type="button" onclick="this.parentElement.remove()"
                style="color:#dc2626;background:none;border:1px solid #fca5a5;border-radius:5px;padding:0.3rem 0.6rem;font-size:0.8rem;cursor:pointer;">&times;</button>
    `;
    container.appendChild(row);
    expenseIndex++;
}
document.getElementById('addExpenseRow').addEventListener('click', addExpenseRow);
</script>
@endsection
