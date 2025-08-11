<x-mail::message>

# Start Earning $5 for Every Referral!

Hi {{ explode(' ', $name)[0] ?? 'there' }},

We're excited to invite you to join our **Referral Program**! As a valued member of our waitlist, you now have the opportunity to earn **$5 digital gift cards** for every person you refer who signs up for a paid CollabConnect plan.

## How It Works:

1. **Share your unique referral code:** `{{ $referralCode }}`
2. **Friends sign up** using your code
3. **They upgrade** to any paid plan
4. **You earn $5** digital gift card instantly!

There's no limit to how much you can earn - the more you refer, the more you make!

## Why Your Friends Will Love CollabConnect:

- **Businesses** find perfect influencer partners for their campaigns
- **Influencers** discover lucrative collaboration opportunities
- Smart matching algorithm connects the right people
- No commission fees - just transparent subscription pricing
- Comprehensive campaign management tools

<x-mail::button :url="config('app.url') . '/refer?code=' . $referralCode">
Start Referring Friends
</x-mail::button>

## Terms & Conditions:
- $5 reward is issued after referred user completes their first paid subscription
- Digital gift cards are delivered via email within 5 business days
- Referred users must be new to CollabConnect
- Standard terms and conditions apply

Ready to start earning? Share your code `{{ $referralCode }}` with friends and family today!

Thanks,<br>
The CollabConnect Team

---

*Having trouble with the button? Copy and paste this link into your browser:*
{{ config('app.url') }}/refer?code={{ $referralCode }}
</x-mail::message>