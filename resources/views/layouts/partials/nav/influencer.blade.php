<flux:navlist>
    <flux:navlist.item href="{{ route('dashboard') }}" icon="home" :current="request()->routeIs('dashboard')">
        Dashboard
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('discover') }}" icon="sparkles" :current="request()->routeIs('discover')">
        Discover
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('search') }}" icon="magnifying-glass" :current="request()->routeIs('search')">
        Find Businesses
    </flux:navlist.item>

    @feature('referral-program')
    <flux:navlist.item href="{{ route('referral.index') }}" icon="user-group" :current="request()->routeIs('referral.*')">
        Referrals
    </flux:navlist.item>
    @endfeature

    <flux:navlist.item href="{{ route('chat.index') }}" icon="chat-bubble-left-right" :current="request()->routeIs('chat.*')" badge="{{ auth()->user()->hasUnreadMessages() ? ' ' : null }}">
        Messages
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('link-in-bio.index') }}" icon="link" :current="request()->routeIs('link-in-bio.*')">
        Link in Bio
    </flux:navlist.item>

    <flux:navlist.group heading="Account" expandable :expanded="request()->routeIs('profile.*') || request()->routeIs('influencer.settings') || request()->routeIs('help')">
        <flux:navlist.item href="{{ route('profile.edit') }}" icon="user" :current="request()->routeIs('profile.edit')">
            Profile
        </flux:navlist.item>

        <flux:navlist.item href="{{ route('influencer.settings') }}" icon="star" :current="request()->routeIs('influencer.settings')">
            Influencer Settings
        </flux:navlist.item>

        <flux:navlist.item href="{{ route('help') }}" icon="question-mark-circle" :current="request()->routeIs('help')">
            Help & Support
        </flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
