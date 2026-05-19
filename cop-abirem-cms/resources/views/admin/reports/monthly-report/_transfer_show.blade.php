{{-- @param string $prefix  transfer_in | transfer_out | water_baptism --}}
{{-- @param MonthlyReport $report --}}
<table class="min-w-full text-sm border border-gray-100 rounded">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-3 py-1.5 text-left text-xs text-gray-500">Age Group</th>
            <th class="px-3 py-1.5 text-center text-xs text-gray-500">Male</th>
            <th class="px-3 py-1.5 text-center text-xs text-gray-500">Female</th>
            <th class="px-3 py-1.5 text-center text-xs text-gray-500 bg-blue-50">Total</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        @foreach(['children' => 'Children', 'teens' => 'Teens', 'young_adults' => 'Young Adults', 'adults' => 'Adults'] as $age => $ageLabel)
        @php
            $m = $report->{$prefix . '_' . $age . '_male'} ?? 0;
            $f = $report->{$prefix . '_' . $age . '_female'} ?? 0;
        @endphp
        <tr>
            <td class="px-3 py-1.5 text-gray-700">{{ $ageLabel }}</td>
            <td class="px-3 py-1.5 text-center">{{ $m }}</td>
            <td class="px-3 py-1.5 text-center">{{ $f }}</td>
            <td class="px-3 py-1.5 text-center font-semibold text-blue-700 bg-blue-50">{{ $m + $f }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
