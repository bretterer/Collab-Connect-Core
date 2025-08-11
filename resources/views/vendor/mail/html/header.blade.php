@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Collab Connect')
<img src="{{ asset('images/CollabConnect.png') }}" class="logo" alt="Collab Connect Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
