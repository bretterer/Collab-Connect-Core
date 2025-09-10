<x-mail::message>

# Welcome to CollabConnect Beta! 🚀

Hi {{ $invite->first_name }},

Congratulations! You've been selected for exclusive early access to **CollabConnect**, the innovative platform revolutionizing how businesses and influencers collaborate.

## What is CollabConnect?

CollabConnect is a cutting-edge marketplace that intelligently connects local businesses with talented influencers, creating authentic partnerships that drive real results.

## Why Join Our Beta?

- 🚀 **Be First:** Experience the future of influencer marketing before anyone else
- ✨ **Premium Access:** All features completely free during the beta period
- 🎯 **Smart Matching:** Our AI-powered algorithm creates perfect partnerships
- 💰 **No Commission:** Keep 100% of what you earn - we don't take cuts
- 🤝 **Direct Support:** Work closely with our team to shape the platform
- 🎁 **Launch Benefits:** Special pricing and perks when we go live

<x-mail::button :url="$signedUrl">
Join CollabConnect Beta Now
</x-mail::button>

**Important:** This secure invitation link expires in 7 days. Register now to secure your exclusive beta access!

## Your Role as a Beta Tester

**CollabConnect is currently in beta**, which means you're getting exclusive early access while we continue to develop and refine the platform. We encourage you to:

- 🧪 **Test all features** - explore every aspect of the platform
- 🐛 **Report any bugs or issues** using the feedback widget in the lower right corner of the screen
- 💡 **Share your ideas** for improvements and new features
- 📝 **Provide feedback** on your experience - your input drives our development priorities

Your participation as a beta user is essential in helping us build the best possible platform for connecting businesses and influencers.

We're excited to have you be part of this journey as we reshape the world of brand collaborations.

Best regards,<br>
The {{ config('app.name') }} Team

---

*This invitation was sent to {{ $invite->email }}. If you believe this was sent in error, please ignore this email.*
</x-mail::message>