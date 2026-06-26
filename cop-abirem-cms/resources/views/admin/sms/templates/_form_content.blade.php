{{--
  Shared template content field with placeholder picker.
  Variables expected: $content (string), $uiRegistry (array from PlaceholderService::uiRegistry())
--}}
<div x-data="templateEditor(@js($content))">
    <div class="flex items-center justify-between mb-1">
        <label for="content" class="block text-sm font-medium text-gray-700">
            Content * <span class="text-gray-400">(max 480 chars)</span>
        </label>

        {{-- Insert Placeholder button --}}
        <div class="relative">
            <button type="button"
                    @click="showMenu = !showMenu"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border border-indigo-300 bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Insert Placeholder
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="showMenu"
                 @click.outside="showMenu = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 z-20 mt-1 w-72 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
                 style="display:none;">

                <div class="px-3 pt-3 pb-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Recipient Fields <span class="text-indigo-500">(auto per person)</span></p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($uiRegistry['recipient'] ?? [] as $key => $def)
                        <button type="button"
                                @click="insert('{{ $key }}')"
                                class="px-2 py-0.5 rounded text-xs font-mono border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                            {{ '{'.$key.'}' }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-100 mx-3 my-2"></div>

                <div class="px-3 pb-1">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Message Fields <span class="text-amber-500">(sender fills in)</span></p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($uiRegistry['manual'] ?? [] as $key => $def)
                        <button type="button"
                                @click="insert('{{ $key }}')"
                                class="px-2 py-0.5 rounded text-xs font-mono border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100">
                            {{ '{'.$key.'}' }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-gray-100 mx-3 my-2"></div>

                <div class="px-3 pb-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Church / System Fields <span class="text-green-500">(auto from settings)</span></p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($uiRegistry['system'] ?? [] as $key => $def)
                        <button type="button"
                                @click="insert('{{ $key }}')"
                                class="px-2 py-0.5 rounded text-xs font-mono border border-green-200 bg-green-50 text-green-700 hover:bg-green-100">
                            {{ '{'.$key.'}' }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <textarea name="content" id="content" rows="5" required maxlength="480"
              x-model="content"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"></textarea>

    <div class="mt-1 flex justify-between text-xs">
        <span class="text-gray-400">Use the button above to insert placeholders. Recipient, message, and system fields all supported.</span>
        <span :class="content.length > 480 ? 'text-red-600 font-semibold' : content.length > 400 ? 'text-amber-500' : 'text-gray-500'"
              x-text="content.length + ' / 480'"></span>
    </div>
    @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

@once
@push('scripts')
<script>
function templateEditor(initialContent) {
    return {
        content: initialContent || '',
        showMenu: false,
        insert(key) {
            const ta    = document.getElementById('content');
            const start = ta.selectionStart;
            const end   = ta.selectionEnd;
            const ph    = '{' + key + '}';
            this.content = ta.value.slice(0, start) + ph + ta.value.slice(end);
            ta.value     = this.content;
            ta.focus();
            ta.selectionStart = ta.selectionEnd = start + ph.length;
            this.showMenu = false;
        },
    };
}
</script>
@endpush
@endonce
