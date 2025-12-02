<x-mail::message>
# Your collaboration is complete!

Hi {{ $recipient->name }},

Great news! The campaign **"{{ $campaignName }}"** has been completed. We'd love to hear about your experience working with **{{ $otherParty->name }}**.

Your feedback helps build trust in our community and helps other {{ $recipientRole === 'business' ? 'businesses' : 'influencers' }} make informed decisions.

## How the review process works:

- You have **{{ $daysRemaining }} days** to submit your review
- Your review will remain **private** until {{ $otherParty->name }} submits theirs (or the review period ends)
- This ensures honest, unbiased feedback from both parties

<x-mail::button :url="$reviewUrl">
Submit Your Review
</x-mail::button>

**Important:** After {{ $daysRemaining }} days, the review period will close and you won't be able to submit a review.

Thanks for being part of the CollabConnect community!

Best regards,<br>
{{ config('app.name') }}

<x-mail::subcopy>
If you're having trouble clicking the "Submit Your Review" button, copy and paste the URL below into your web browser: [{{ $reviewUrl }}]({{ $reviewUrl }})
</x-mail::subcopy>
</x-mail::message>
