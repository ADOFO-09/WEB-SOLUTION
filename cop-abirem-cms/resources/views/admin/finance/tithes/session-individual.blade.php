@extends('layouts.admin')

@section('title', 'Individual Session Tithes')

@section('header')
<div class="flex items-center justify-between">
    <div class="flex items-center">
        <a href="{{ route('admin.finance.dashboard') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Individual Session Tithes</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $session->serviceType->name ?? 'Service' }}
                &mdash; {{ $session->service_date->format('l, d M Y') }}
                @if($session->status === 'open')
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Open</span>
                @else
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Closed</span>
                @endif
            </p>
        </div>
    </div>
    <a href="{{ route('admin.tithes.session.create') }}"
       class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        Switch session
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ── Entry form (left / top) ─────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="card sticky top-4">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">Record Tithe</h3>
                <span class="text-xs text-gray-400">Form resets after each entry</span>
            </div>
            <div class="card-body space-y-4">

                @if(session('success'))
                <div class="alert alert-success text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>{{ $errors->first() }}</div>
                </div>
                @endif

                <form action="{{ route('admin.tithes.session.individual.store', $session) }}" method="POST">
                    @csrf

                    {{-- Member searchable combobox --}}
                    <div class="form-group"
                         x-data="memberCombobox({{ $members->map(fn($m) => ['id' => $m->id, 'name' => $m->full_name, 'code' => $m->member_id])->values()->toJson() }}, '{{ old('member_id') }}')"
                         @click.outside="close()">
                        <label class="form-label">Member <span class="text-red-500">*</span></label>

                        <div class="relative">
                            {{-- Search input --}}
                            <input type="text"
                                   x-model="query"
                                   @focus="open = true"
                                   @input="open = true"
                                   @keydown.escape="close()"
                                   @keydown.enter.prevent="confirmTop()"
                                   @keydown.arrow-down.prevent="moveDown()"
                                   @keydown.arrow-up.prevent="moveUp()"
                                   :placeholder="selected ? selected.name + ' (' + selected.code + ')' : 'Type name or member ID…'"
                                   :class="selected ? 'border-indigo-400 bg-indigo-50' : ''"
                                   class="form-input pr-8"
                                   autocomplete="off">

                            {{-- Clear button --}}
                            <button type="button"
                                    x-show="selected"
                                    @click="clear()"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
                                    tabindex="-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            {{-- Hidden real input --}}
                            <input type="hidden" name="member_id" :value="selected ? selected.id : ''" required>

                            {{-- Dropdown --}}
                            <div x-show="open && filtered.length > 0"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto">
                                <template x-for="(m, i) in filtered" :key="m.id">
                                    <div @click="pick(m)"
                                         :class="i === cursor ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-gray-50'"
                                         class="flex items-center justify-between px-3 py-2 cursor-pointer text-sm">
                                        <span x-text="m.name" class="font-medium"></span>
                                        <span x-text="m.code" class="text-xs text-gray-400 font-mono ml-2"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- No results --}}
                            <div x-show="open && query.length > 0 && filtered.length === 0"
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow px-3 py-2 text-sm text-gray-400">
                                No members found for "<span x-text="query"></span>"
                            </div>
                        </div>

                        @error('member_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="form-group">
                        <label class="form-label">Amount <span class="text-red-500">*</span></label>
                        <div class="flex rounded-lg border border-gray-300 overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                            <span class="inline-flex items-center px-3 bg-gray-100 border-r border-gray-300 text-gray-600 font-medium text-sm select-none whitespace-nowrap">
                                {{ $currencySymbol }}
                            </span>
                            <input type="number" name="amount" step="0.01" min="0.01" required
                                   value="{{ old('amount') }}"
                                   class="flex-1 px-3 py-2 text-base border-0 outline-none focus:ring-0 bg-white"
                                   placeholder="0.00">
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="form-group">
                        <label class="form-label">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            @include('admin.partials.payment-method-options', ['selected' => old('payment_method', 'cash')])
                        </select>
                    </div>

                    {{-- Reference --}}
                    <div class="form-group">
                        <label class="form-label">Payment Reference <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="payment_reference" class="form-input"
                               placeholder="MoMo ID, cheque no., etc."
                               value="{{ old('payment_reference') }}">
                    </div>

                    {{-- Notes --}}
                    <div class="form-group">
                        <label class="form-label">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="notes" class="form-input"
                               placeholder="Any notes…"
                               value="{{ old('notes') }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-full mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Record Tithe
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Session tithe list (right) ──────────────────────── --}}
    <div class="lg:col-span-3">

        {{-- Summary strip --}}
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="stat-card text-center">
                <div class="stat-card-value">{{ $sessionTithes->count() }}</div>
                <div class="stat-card-label">Entries</div>
            </div>
            <div class="stat-card text-center">
                <div class="stat-card-value">{{ $currencySymbol }} {{ number_format($sessionTithes->sum('amount'), 2) }}</div>
                <div class="stat-card-label">Total Collected</div>
            </div>
            <div class="stat-card text-center">
                <div class="stat-card-value">
                    {{ $members->count() > 0 ? round(($sessionTithes->count() / $members->count()) * 100) : 0 }}%
                </div>
                <div class="stat-card-label">Members Covered</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">Tithes for This Session</h3>
                <span class="badge badge-info">{{ $sessionTithes->count() }} recorded</span>
            </div>

            @if($sessionTithes->isEmpty())
            <div class="p-10 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">No individual tithes recorded for this session yet.</p>
                <p class="text-xs mt-1">Use the form on the left to begin.</p>
            </div>
            @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>Method</th>
                            <th>Receipt</th>
                            <th class="text-right">Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessionTithes as $i => $tithe)
                        <tr>
                            <td class="text-gray-400 text-xs">{{ $i + 1 }}</td>
                            <td>
                                <p class="font-medium text-gray-800 text-sm">{{ $tithe->member->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $tithe->member->member_id }}</p>
                            </td>
                            <td class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</td>
                            <td class="font-mono text-xs text-gray-500">{{ $tithe->receipt_number }}</td>
                            <td class="text-right font-semibold text-green-700">{{ $currencySymbol }} {{ number_format($tithe->amount, 2) }}</td>
                            <td>
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('admin.tithes.show', $tithe) }}"
                                       class="text-xs text-indigo-600 hover:underline">View</a>
                                    <form action="{{ route('admin.tithes.destroy', $tithe) }}" method="POST"
                                          onsubmit="return confirm('Delete this tithe entry?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 font-bold">
                            <td colspan="4" class="text-gray-700">Session Total</td>
                            <td class="text-right text-green-700">{{ $currencySymbol }} {{ number_format($sessionTithes->sum('amount'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function memberCombobox(members, oldId) {
    return {
        members,
        query: '',
        open: false,
        cursor: -1,
        selected: oldId ? (members.find(m => m.id == oldId) ?? null) : null,

        get filtered() {
            if (!this.query) return this.members.slice(0, 50);
            const q = this.query.toLowerCase();
            return this.members
                .filter(m => m.name.toLowerCase().includes(q) || m.code.toLowerCase().includes(q))
                .slice(0, 50);
        },

        pick(m) {
            this.selected = m;
            this.query    = '';
            this.open     = false;
            this.cursor   = -1;
        },

        clear() {
            this.selected = null;
            this.query    = '';
            this.cursor   = -1;
        },

        close() {
            this.open   = false;
            this.cursor = -1;
        },

        confirmTop() {
            const list = this.filtered;
            const idx  = this.cursor >= 0 ? this.cursor : 0;
            if (list[idx]) this.pick(list[idx]);
        },

        moveDown() {
            this.open   = true;
            this.cursor = Math.min(this.cursor + 1, this.filtered.length - 1);
        },

        moveUp() {
            this.cursor = Math.max(this.cursor - 1, 0);
        },
    };
}

@if(session('success'))
// Return focus to the search input after a successful save
document.querySelector('[x-data]')
    ?._x_dataStack?.[0]
    ?? document.querySelector('input[placeholder*="Type name"]')?.focus();
setTimeout(() => document.querySelector('input[placeholder*="Type name"]')?.focus(), 100);
@endif
</script>
@endpush
