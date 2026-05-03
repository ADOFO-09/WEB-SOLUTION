@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel' || trim($slot) === config('app.name'))
<span style="font-family: Georgia, 'Times New Roman', serif; font-size: 1.3rem; font-weight: 700; color: #1e3a5f; letter-spacing: -0.02em;">
    Church of Pentecost <span style="color: #d4af37;">·</span> Abirem
</span>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
