<x-mail::message>
{!! $body !!}

@if($unsubscribeUrl)
<x-mail::subcopy>
If you'd like to stop receiving these emails, you can [unsubscribe here]({{ $unsubscribeUrl }}).
</x-mail::subcopy>
@endif

{{-- Tracking pixel for opens --}}
<img src="{{ route('email-sequence.track', ['send' => $sendId]) }}" width="1" height="1" alt="" style="display:none;" />
</x-mail::message>
