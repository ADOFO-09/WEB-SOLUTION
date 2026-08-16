@extends('layouts.admin')
@section('title', 'Ministry Expenses')

@section('content')
<div class="mb-6" style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">{{ $ministry->name }} — Expenses</h1>
        <p style="color:#64748b;margin-top:0.25rem;">Record and manage ministry expenses & welfare payments</p>
    </div>
    <div style="display:flex;gap:0.5rem;">
        <a href="{{ route('admin.ministry.finance.expenses.pdf', request()->only(['year','category','month'])) }}" class="btn btn-secondary" title="Export PDF">
            <svg style="width:1rem;height:1rem;margin-right:0.4rem;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            Export PDF
        </a>
        <a href="{{ route('admin.ministry.finance.index') }}" class="btn btn-secondary">← Finance Home</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:340px 1fr;gap:1.5rem;align-items:start;">

    {{-- Record Expense Form --}}
    <div class="card">
        <div class="card-header"><h3 style="font-weight:600;color:#1e3a5f;">Record Expense</h3></div>
        <div class="card-body">
            <form action="{{ route('admin.ministry.finance.expenses.store') }}" method="POST">
                @csrf
                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul style="margin:0;padding-left:1.25rem;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Category <span style="color:#ef4444;">*</span></label>
                    <select name="category" class="form-select" required>
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}" @selected(old('category') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="description" class="form-input"
                           value="{{ old('description') }}" placeholder="e.g. Welfare support for Bro. Mensah" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount (GH₵) <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="amount" class="form-input" step="0.01" min="0.01"
                           value="{{ old('amount') }}" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Date <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="expense_date" class="form-input"
                           value="{{ old('expense_date', now()->toDateString()) }}"
                           max="{{ now()->toDateString() }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Paid To</label>
                    <input type="text" name="paid_to" class="form-input"
                           value="{{ old('paid_to') }}" placeholder="Name of recipient">
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method <span style="color:#ef4444;">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        @foreach($methods as $val => $label)
                            <option value="{{ $val }}" @selected(old('payment_method', 'cash') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Reference No.</label>
                    <input type="text" name="payment_reference" class="form-input"
                           value="{{ old('payment_reference') }}" placeholder="Optional">
                </div>

                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-input" rows="2" placeholder="Optional notes">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;">Save Expense</button>
            </form>
        </div>
    </div>

    {{-- Expenses List --}}
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
            <h3 style="font-weight:600;color:#1e3a5f;">Expense Records</h3>
            <span style="font-size:0.85rem;color:#64748b;">Total shown: <strong style="color:#dc2626;">GH₵ {{ number_format($totalShown, 2) }}</strong></span>
        </div>

        {{-- Filters --}}
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;">
                <div>
                    <label style="font-size:0.75rem;color:#64748b;display:block;margin-bottom:0.25rem;">Category</label>
                    <select name="category" class="form-select" style="font-size:0.8rem;" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $val => $label)
                            <option value="{{ $val }}" @selected(request('category') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.75rem;color:#64748b;display:block;margin-bottom:0.25rem;">Month</label>
                    <select name="month" class="form-select" style="font-size:0.8rem;" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $mn)
                            <option value="{{ $i+1 }}" @selected(request('month') == $i+1)>{{ $mn }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.75rem;color:#64748b;display:block;margin-bottom:0.25rem;">Year</label>
                    <select name="year" class="form-select" style="font-size:0.8rem;" onchange="this.form.submit()">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" @selected(request('year', now()->year) == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <a href="{{ route('admin.ministry.finance.expenses') }}" class="btn btn-secondary btn-sm" style="margin-top:auto;">Reset</a>
            </form>
        </div>

        <div class="card-body" style="padding:0;">
            @if($expenses->isEmpty())
                <div style="padding:2rem;text-align:center;color:#94a3b8;">No expenses found for the selected filters.</div>
            @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Paid To</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $e)
                    <tr>
                        <td style="font-size:0.8rem;color:#64748b;">{{ $e->reference_number }}</td>
                        <td>{{ $e->expense_date->format('M d, Y') }}</td>
                        <td>
                            @php
                                $catColor = $e->category === 'welfare_payment'
                                    ? 'background:#fce7f3;color:#9d174d;'
                                    : 'background:#fef3c7;color:#92400e;';
                            @endphp
                            <span style="font-size:0.75rem;padding:0.2rem 0.6rem;border-radius:9999px;font-weight:500;{{ $catColor }}">
                                {{ $categories[$e->category] ?? $e->category }}
                            </span>
                        </td>
                        <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $e->description }}">
                            {{ $e->description }}
                        </td>
                        <td style="font-size:0.85rem;color:#64748b;">{{ $e->paid_to ?? '—' }}</td>
                        <td style="font-weight:600;color:#dc2626;">GH₵ {{ number_format($e->amount, 2) }}</td>
                        <td>
                            <form action="{{ route('admin.ministry.finance.expenses.delete', $e->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this expense record?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:0.8rem;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        @if($expenses->hasPages())
        <div style="padding:1rem 1.25rem;border-top:1px solid #f1f5f9;">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
