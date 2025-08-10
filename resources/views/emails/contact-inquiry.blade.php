<x-mail::message>
# New Contact Inquiry from CollabConnect

You've received a new contact inquiry through the CollabConnect website.

## Contact Details
**Name:** {{ $firstName }} {{ $lastName }}
**Email:** {{ $email }}
**Subject:** {{ $subject }}
**Newsletter Signup:** {{ $newsletter ? 'Yes' : 'No' }}

## Message
{{ $message }}

---

**What to do next:**
1. Reply directly to this email to respond to {{ $firstName }}
2. The customer expects a response within {{ config('collabconnect.support_response_days') }} business {{ config('collabconnect.support_response_days') == 1 ? 'day' : 'days' }}
3. Consider their newsletter preference for future communications

<x-mail::button :url="'mailto:' . $email">
Reply to {{ $firstName }}
</x-mail::button>

This message was sent via the CollabConnect contact form.

Thanks,<br>
{{ config('app.name') }} Support System
</x-mail::message>
