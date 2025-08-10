@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@elseif (trim($slot) === config('app.name') || str_contains(trim($slot), 'CollabConnect'))
<img src="{{ config('app.url') }}/images/CollabConnect.png" class="logo" alt="CollabConnect Logo" style="height: 50px; width: auto;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
