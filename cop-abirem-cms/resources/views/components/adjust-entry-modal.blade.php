@props(['entryType', 'entryId', 'reference', 'currentAmount', 'route'])

<div x-data="{ open: false }">

    {{-- Trigger button --}}
    <button type="button"
            @click="open = true"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded text-yellow-700 bg-yellow-50 border border-yellow-200 hover:bg-yellow-100 transition">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Adjust
    </button>

    {{-- Backdrop --}}
    <div x-show="open"
         @click="open = false"
         class="fixed inset-0"
         style="display:none;background:rgba(0,0,0,0.55);z-index:9998;">
    </div>

    {{-- Dialog --}}
    <div x-show="open"
         @keydown.escape.window="open = false"
         class="fixed inset-0 flex items-center justify-center p-4"
         style="display:none;z-index:9999;pointer-events:none;">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md pointer-events-auto"
             @click.stop>

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 pt-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Adjust Entry</h3>
                    <p class="text-xs text-gray-500">
                        <span class="font-mono">{{ $reference }}</span>
                        &mdash; current amount: <strong class="text-gray-700">GH₵ {{ number_format($currentAmount, 2) }}</strong>
                    </p>
                </div>
                <button type="button" @click="open = false"
                        class="ml-auto text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 mb-4">
                    This creates a correcting entry (<strong class="font-mono text-blue-700">ADJ-{{ $reference }}</strong>) with the new amount.
                    The original entry is marked <span class="font-semibold text-yellow-700">ADJ</span> and excluded from totals.
                </p>

                <form method="POST" action="{{ $route }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Corrected Amount (GH₵) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="amount"
                               step="0.01"
                               min="0.01"
                               required
                               placeholder="{{ number_format($currentAmount, 2) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-400">Enter the correct amount, not the difference.</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Corrected Date <span class="text-gray-400 font-normal">(optional — leave blank to keep original)</span>
                        </label>
                        <input type="date"
                               name="payment_date"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Reason for adjustment <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason"
                                  rows="3"
                                  required
                                  minlength="10"
                                  maxlength="500"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent resize-none"
                                  placeholder="Minimum 10 characters — e.g. Wrong amount entered, correct amount is GH₵ ..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button"
                                @click="open = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 transition">
                            Create Adjustment
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
