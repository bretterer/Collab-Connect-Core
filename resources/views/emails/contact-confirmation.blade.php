<x-mail::message>
# Thank You for Contacting CollabConnect!

Hi {{ $firstName }},

We've received your message regarding "{{ $subject }}" and wanted to confirm that it's safely in our inbox.

## What happens next?
- **Response Time:** We'll get back to you within {{ config('collabconnect.support_response_days') }} business {{ config('collabconnect.support_response_days') == 1 ? 'day' : 'days' }}
- **Business Hours:** Monday - Friday, 9 AM - 6 PM EST
- **Priority:** Your inquiry is important to us and will be handled promptly

## Your Inquiry Details
**Subject:** {{ $subject }}
**Submitted:** {{ now()->format('F j, Y \a\t g:i A T') }}

If you have any urgent questions or need immediate assistance, please don't hesitate to reach out to us directly.

<x-mail::button :url="'https://collabconnect.test'">
Visit CollabConnect
</x-mail::button>

We appreciate your interest in CollabConnect and look forward to helping you!

Best regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
