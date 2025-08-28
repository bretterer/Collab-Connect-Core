<x-mail::message>

# You're Invited to Join {{ $invite->business->name }}! 🤝

Hi there,

You've been invited by **{{ $invite->invitedBy->name }}** to join their business team on **CollabConnect**.

## About {{ $invite->business->name }}

{{ $invite->business->name }} is using CollabConnect to connect with talented influencers for collaborative marketing campaigns. As a team member, you'll help manage campaigns, review applications, and build meaningful partnerships with content creators.

## Your Invitation Details

- **Business:** {{ $invite->business->name }}
- **Role:** {{ ucfirst($invite->role) }}
- **Invited by:** {{ $invite->invitedBy->name }}
- **Invited on:** {{ $invite->invited_at->format('F j, Y') }}

## What's Next?

Click the button below to accept this invitation and join the team. You'll be able to:

- 👥 **Collaborate** with the business team
- 📊 **Manage campaigns** and review influencer applications  
- 🎯 **Help grow** the business through strategic partnerships
- 💼 **Access** all business tools and features

<x-mail::button :url="$signedUrl">
Accept Invitation
</x-mail::button>

**Important:** This invitation link is secure and expires in 7 days. If you already have a CollabConnect account, you'll be prompted to accept or decline this invitation after logging in.

If you don't have an account yet, you can create one using this invitation link and you'll automatically be added to the business team.

Have questions? Feel free to reach out to **{{ $invite->invitedBy->name }}** who sent this invitation.

Best regards,<br>
The {{ config('app.name') }} Team

---

*This invitation was sent to {{ $invite->email }}. If you believe this was sent in error, please ignore this email.*

</x-mail::message>