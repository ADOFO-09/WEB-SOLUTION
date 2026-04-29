@props(['entryType', 'entryId', 'reference', 'route'])

<div x-data="{ open: false }">

    {{-- Trigger button --}}
    <button type="button"
            @click="open = true"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded text-red-700 bg-red-50 border border-red-200 hover:bg-red-100 transition">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
        Void
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
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Void Entry</h3>
                    <p class="text-xs text-gray-500 font-mono">{{ $reference }}</p>
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
                    This will <strong class="text-gray-900">void</strong> the entry. It will remain visible in the ledger
                    but be excluded from all totals. You cannot undo this without the Restore action.
                </p>

                <form method="POST" action="{{ $route }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Reason for voiding <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason"
                                  rows="3"
                                  required
                                  minlength="10"
                                  maxlength="500"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none"
                                  placeholder="Minimum 10 characters — describe why this entry is being voided..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button"
                                @click="open = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
                            Void Entry
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
