<x-mail::message>
# Welcome to the CollabConnect Beta! 🚀

Hi {{ $name }},

Thank you for joining the CollabConnect beta waitlist! We're excited to have you as one of our early {{ $userType === 'business' ? 'business partners' : 'content creators' }}.

## What happens next?

@if($userType === 'business')
### For Business Owners 🏢
- **Early Access:** You'll be among the first to access our platform when we launch
- **Special Pricing:** Beta users get exclusive pricing during the launch period
- **Direct Support:** Priority customer support during the beta phase
- **Feature Input:** Help shape the platform with your feedback and suggestions
@if($businessName)

We're particularly excited to work with **{{ $businessName }}** to transform your local marketing strategy!
@endif

@else
### For Influencers & Creators 📱
- **Beta Access:** Be among the first influencers on our platform
- **Priority Matching:** Get first access to local business partnerships
- **Higher Rates:** Beta creators often command premium rates for early partnerships
- **Community Building:** Help us build Cincinnati & Dayton's premier creator network
@if($followerCount)

With your {{ $followerCount }} followers, you're exactly the kind of creator we're looking for!
@endif

@endif

## Timeline & Next Steps
- **Beta Launch:** We're planning to launch in **late 2025**
- **Notifications:** You'll receive updates as we get closer to launch
- **Preparation:** Start thinking about your {{ $userType === 'business' ? 'marketing goals and budget' : 'content style and pricing' }}

## Stay Connected
Follow our progress and connect with other beta members:

<x-mail::button :url="'https://collabconnect.test'">
Visit CollabConnect
</x-mail::button>

We'll keep you updated on our progress and send you exclusive beta access when we're ready to launch.

**Thank you for believing in the future of local marketing!**

Best regards,<br>
The {{ config('app.name') }} Team

---

*P.S. Keep an eye on your inbox - we'll be sharing behind-the-scenes updates and beta previews with our early supporters!*
</x-mail::message>
