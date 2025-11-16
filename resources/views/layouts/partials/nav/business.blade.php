<flux:navlist>
    <flux:navlist.item href="{{ route('dashboard') }}" icon="home" :current="request()->routeIs('dashboard')">
        Dashboard
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('campaigns.index') }}" icon="document-text" :current="request()->routeIs('campaigns.*')">
        Campaigns
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('applications.index') }}" icon="clipboard-document-list" :current="request()->routeIs('applications.*')">
        Applications
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('search') }}" icon="magnifying-glass" :current="request()->routeIs('search')">
        Find Influencers
    </flux:navlist.item>

    @feature('referral-program')
    <flux:navlist.item href="{{ route('referral.index') }}" icon="user-group" :current="request()->routeIs('referral.*')">
        Referrals
    </flux:navlist.item>
    @endfeature

    <flux:navlist.item href="{{ route('chat.index') }}" icon="chat-bubble-left-right" :current="request()->routeIs('chat.*')" badge="{{ auth()->user()->hasUnreadMessages() ? ' ' : null }}">
        Messages
    </flux:navlist.item>

    <flux:navlist.group heading="Account" expandable :expanded="request()->routeIs('profile.*') || request()->routeIs('help')">
        <flux:navlist.item href="{{ route('profile.edit') }}" icon="user" :current="request()->routeIs('profile.edit')">
            Profile
        </flux:navlist.item>

        <flux:navlist.item href="{{ route('help') }}" icon="question-mark-circle" :current="request()->routeIs('help')">
            Help & Support
        </flux:navlist.item>
    </flux:navlist.group>
</flux:navlist>
