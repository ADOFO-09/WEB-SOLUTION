{{-- @param string $prefix  transfer_in | transfer_out --}}
{{-- @param callable $val --}}
<div class="overflow-x-auto">
    <table class="min-w-full text-sm border border-gray-200 rounded">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Age Group</th>
                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Male</th>
                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Female</th>
                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 bg-blue-50">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach(['children' => 'Children', 'teens' => 'Teens', 'young_adults' => 'Young Adults', 'adults' => 'Adults'] as $age => $ageLabel)
            @php
                $mField = $prefix . '_' . $age . '_male';
                $fField = $prefix . '_' . $age . '_female';
                $mVal   = (int) $val($mField);
                $fVal   = (int) $val($fField);
            @endphp
            <tr x-data="{ m: {{ $mVal }}, f: {{ $fVal }} }">
                <td class="px-3 py-2 font-medium text-gray-700">{{ $ageLabel }}</td>
                <td class="px-3 py-2">
                    <input type="number" name="{{ $mField }}" x-model.number="m" min="0"
                           class="w-20 rounded border-gray-300 text-sm text-center mx-auto block">
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="{{ $fField }}" x-model.number="f" min="0"
                           class="w-20 rounded border-gray-300 text-sm text-center mx-auto block">
                </td>
                <td class="px-3 py-2 text-center font-semibold text-blue-700 bg-blue-50" x-text="m + f"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
