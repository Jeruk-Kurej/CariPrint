@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<span class="brand-mark" style="color: #3558f0;">&#9678;</span> {!! $slot !!}
</a>
</td>
</tr>
