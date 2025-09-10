<x-mail::message>

# Welcome to CollabConnect Beta! 🚀

Hi {{ $invite->first_name }},

Congratulations! You've been selected for exclusive early access to **CollabConnect**, the revolutionary platform that connects local businesses with talented influencers.

## Why CollabConnect for Businesses?

- 🎯 **Smart Matching:** Our AI-powered algorithm finds influencers who perfectly align with your brand, location, and campaign goals
- 📊 **Transparent Metrics:** Clear insights into reach, engagement, and ROI for every collaboration
- 💰 **Cost-Effective:** No commission fees - just pay what you agree with influencers
- 🏢 **Local Focus:** Connect with influencers in your community for authentic, location-based marketing
- ⚡ **Easy Campaign Management:** Streamlined tools to create, manage, and track all your influencer partnerships

## As a beta user, you'll get:

- ✨ **Free Premium Features** during the beta period
- 🤝 **Direct Access** to our team for support and feedback
- 🎁 **Special Launch Pricing** when we go live
- 🔧 **Influence Product Development** with your feedback

<x-mail::button :url="$signedUrl">
Join CollabConnect Beta Now
</x-mail::button>

**Important:** This invitation link is secure and expires in 7 days. Please register as soon as possible to secure your beta access.

## Help Us Build Something Amazing

**CollabConnect is currently in beta**, which means you're getting an exclusive preview of our platform as we continue to refine and improve it. We encourage you to:

- 🧪 **Test everything** - explore all features and functionality
- 🐛 **Report any issues** you encounter using the feedback widget in the lower right corner of the screen
- 💡 **Share your ideas** for improvements and new features
- 📝 **Give us feedback** on your experience - your input directly shapes our development

Your participation as a beta user is invaluable in helping us create the best possible platform for businesses and influencers.

Ready to transform your marketing strategy? We can't wait to see the amazing collaborations you'll create!

Best regards,<br>
The {{ config('app.name') }} Team

---

*This invitation was sent to {{ $invite->email }}. If you believe this was sent in error, please ignore this email.*

</x-mail::message>