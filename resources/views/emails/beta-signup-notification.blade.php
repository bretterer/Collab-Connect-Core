<x-mail::message>
# New Beta Signup! 🚀

A new {{ $userType === 'business' ? 'business owner' : 'influencer' }} has joined the CollabConnect beta waitlist.

## Signup Details
**Name:** {{ $name }}
**Email:** {{ $email }}
**User Type:** {{ ucfirst($userType) }}
@if($userType === 'business' && $businessName)
**Business Name:** {{ $businessName }}
@endif
@if($userType === 'influencer' && $followerCount)
**Follower Count:** {{ $followerCount }}
@endif
**Signed Up:** {{ now()->format('F j, Y \a\t g:i A T') }}

---

## Next Steps
1. Review their profile for fit with CollabConnect
2. Consider reaching out personally for high-value prospects
3. Add to the beta launch communication list
4. Monitor signup trends and demographics

<x-mail::button :url="'mailto:' . $email">
Reply to {{ $name }}
</x-mail::button>

**Growing our beta community one signup at a time!**

Track signup progress in your waitlist CSV file.

Thanks,<br>
{{ config('app.name') }} Beta Team
</x-mail::message>
