<x-mail::message>

# Welcome to CollabConnect Beta! 🚀

Hi {{ $invite->first_name }},

Congratulations! You've been selected for exclusive early access to **CollabConnect**, the game-changing platform that connects talented influencers with local businesses for meaningful collaborations.

## Why CollabConnect for Influencers?

- 🎯 **Perfect Brand Matches:** Our smart algorithm connects you with businesses that align with your niche, values, and audience
- 💰 **Fair Compensation:** Transparent pricing with no hidden commission cuts - keep what you earn
- 🏠 **Local Opportunities:** Discover amazing brands and businesses right in your community
- 📈 **Grow Your Influence:** Access tools and insights to track your success and build stronger partnerships
- ⚡ **Streamlined Workflow:** Easy application process and campaign management tools

## As a beta influencer, you'll enjoy:

- ✨ **Premium Features** completely free during beta
- 🚀 **First Access** to the best local brand partnerships
- 💬 **Direct Communication** with our team for support
- 🎁 **Exclusive Perks** and special launch benefits
- 📝 **Shape the Platform** with your valuable feedback

<x-mail::button :url="$signedUrl">
Join CollabConnect Beta Now
</x-mail::button>

**Important:** This invitation link is secure and expires in 7 days. Don't miss out on this exclusive opportunity to be part of the future of influencer marketing!

Ready to discover your next amazing brand partnership? Let's make some magic happen!

Best regards,<br>
The {{ config('app.name') }} Team

---

*This invitation was sent to {{ $invite->email }}. If you believe this was sent in error, please ignore this email.*

</x-mail::message>