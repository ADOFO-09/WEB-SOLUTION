@extends('layouts.admin')

@section('title', 'Manage Financial Particulars')

@section('content')
<div class="space-y-6">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:#1e3a5f;">Financial Particulars</h1>
        <p style="color:#64748b;">Manage income categories and expense categories used in ledgers.</p>
    </div>

    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:.5rem;padding:1rem;color:#15803d;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:.5rem;padding:1rem;color:#991b1b;">{{ session('error') }}</div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        {{-- ===== INCOME PARTICULARS ===== --}}
        <div>
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-weight:600;color:#1e3a5f;">Income Particulars</h3>
                    <span style="font-size:.75rem;color:#64748b;">Used in Income Ledger</span>
                </div>
                <div class="card-body">
                    {{-- Add new income particular --}}
                    <form action="{{ route('admin.finance.particulars.store.income') }}" method="POST" style="margin-bottom:1.5rem;">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr auto;gap:.5rem;margin-bottom:.5rem;">
                            <input type="text" name="name" class="form-input" placeholder="Particular name..." required>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                        <select name="type" class="form-select" required>
                            <option value="offering">Regular Offering</option>
                            <option value="tithe">Tithe</option>
                            <option value="special">Special Offering</option>
                            <option value="donation">Donation</option>
                            <option value="pledge">Pledge</option>
                            <option value="other">Other</option>
                        </select>
                        @error('name')<p style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</p>@enderror
                    </form>

                    {{-- List grouped by type --}}
                    @php
                    $typeLabels = [
                        'tithe'    => ['label' => 'Tithe', 'bg' => '#dcfce7', 'text' => '#15803d'],
                        'offering' => ['label' => 'Offering', 'bg' => '#dbeafe', 'text' => '#1d4ed8'],
                        'special'  => ['label' => 'Special Offering', 'bg' => '#fef3c7', 'text' => '#b45309'],
                        'donation' => ['label' => 'Donation', 'bg' => '#ede9fe', 'text' => '#6d28d9'],
                        'pledge'   => ['label' => 'Pledge', 'bg' => '#e0e7ff', 'text' => '#4338ca'],
                        'other'    => ['label' => 'Other', 'bg' => '#f1f5f9', 'text' => '#475569'],
                    ];
                    @endphp

                    @foreach($typeLabels as $typeKey => $typeInfo)
                    @if(isset($incomeByType[$typeKey]) && $incomeByType[$typeKey]->count())
                    <div style="margin-bottom:1rem;">
                        <div style="font-size:.75rem;font-weight:600;background:{{ $typeInfo['bg'] }};color:{{ $typeInfo['text'] }};padding:.25rem .75rem;border-radius:9999px;display:inline-block;margin-bottom:.5rem;">
                            {{ $typeInfo['label'] }}
                        </div>
                        @foreach($incomeByType[$typeKey] as $category)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border:1px solid #e2e8f0;border-radius:.5rem;margin-bottom:.25rem;background:{{ $category->is_active ? 'white' : '#f8fafc' }};">
                            <div>
                                <span style="font-size:.875rem;color:{{ $category->is_active ? '#1e293b' : '#94a3b8' }};">{{ $category->name }}</span>
                                @if($category->is_system)
                                <span style="font-size:.625rem;background:#e2e8f0;color:#64748b;padding:.125rem .375rem;border-radius:9999px;margin-left:.25rem;">system</span>
                                @endif
                            </div>
                            <div style="display:flex;gap:.25rem;">
                                <form action="{{ route('admin.finance.particulars.toggle.income', $category) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" style="font-size:.75rem;padding:.125rem .5rem;border:1px solid {{ $category->is_active ? '#86efac' : '#e2e8f0' }};border-radius:.375rem;background:{{ $category->is_active ? '#f0fdf4' : '#f8fafc' }};color:{{ $category->is_active ? '#16a34a' : '#64748b' }};cursor:pointer;">
                                        {{ $category->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </form>
                                @if(!$category->is_system)
                                <form action="{{ route('admin.finance.particulars.destroy.income', $category) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ addslashes($category->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="font-size:.75rem;padding:.125rem .5rem;border:1px solid #fca5a5;border-radius:.375rem;background:#fef2f2;color:#dc2626;cursor:pointer;">✕</button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== EXPENSE CATEGORIES ===== --}}
        <div>
            <div class="card">
                <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="font-weight:600;color:#1e3a5f;">Expense Categories</h3>
                    <span style="font-size:.75rem;color:#64748b;">Used in Expense Ledger</span>
                </div>
                <div class="card-body">
                    {{-- Add new expense category --}}
                    <form action="{{ route('admin.finance.particulars.store.expense') }}" method="POST" style="margin-bottom:1.5rem;">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr auto;gap:.5rem;">
                            <input type="text" name="name" class="form-input" placeholder="Category name..." required>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                        @error('name')<p style="color:#dc2626;font-size:.75rem;margin-top:.25rem;">{{ $message }}</p>@enderror
                    </form>

                    {{-- List --}}
                    @foreach($expenseCategories as $cat)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;border:1px solid #e2e8f0;border-radius:.5rem;margin-bottom:.25rem;background:white;">
                        <span style="font-size:.875rem;color:#1e293b;">{{ $cat->name }}</span>
                        <form action="{{ route('admin.finance.particulars.destroy.expense', $cat) }}" method="POST"
                              onsubmit="return confirm('Delete {{ addslashes($cat->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="font-size:.75rem;padding:.125rem .5rem;border:1px solid #fca5a5;border-radius:.375rem;background:#fef2f2;color:#dc2626;cursor:pointer;">✕</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
