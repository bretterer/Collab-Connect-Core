<flux:navlist>
    @if(auth()->user()->account_type !== App\Enums\AccountType::ADMIN)
    <flux:navlist.item href="{{ route('dashboard') }}" icon="arrow-left" :current="request()->routeIs('dashboard')">
        Back to App
    </flux:navlist.item>
    @endif

    <flux:navlist.item href="{{ route('admin.dashboard') }}" icon="home" :current="request()->routeIs('admin.dashboard')">
        Admin Dashboard
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.users.index') }}" icon="users" :current="request()->routeIs('admin.users.*')">
        Users
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.businesses.index') }}" icon="building-office" :current="request()->routeIs('admin.businesses.*')">
        Businesses
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.influencers.index') }}" icon="sparkles" :current="request()->routeIs('admin.influencers.*')">
        Influencers
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.audit-log') }}" icon="clipboard-document-list" :current="request()->routeIs('admin.audit-log')">
        Audit Log
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.beta-invites') }}" icon="envelope" :current="request()->routeIs('admin.beta-invites')">
        Beta Invites
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.feedback') }}" icon="chat-bubble-left" :current="request()->routeIs('admin.feedback')">
        Feedback
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.affiliates.index') }}" icon="user-group" :current="request()->routeIs('admin.affiliates.*')">
        Affiliates
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.custom-signup-pages.index') }}" icon="user-plus" :current="request()->routeIs('admin.custom-signup-pages.*')">
        Custom Signup Pages
    </flux:navlist.item>

    <flux:navlist.group heading="Pricing" expandable :expanded="request()->routeIs('admin.pricing.*')">
        <flux:navlist.item href="{{ route('admin.pricing') }}" icon="document" :current="request()->routeIs('admin.pricing')">
            Pricing
        </flux:navlist.item>

    </flux:navlist.group>

    <flux:navlist.group heading="Markets" expandable :expanded="request()->routeIs('admin.markets.*')">
        <flux:navlist.item href="{{ route('admin.markets.index') }}" icon="document" :current="request()->routeIs('admin.markets.index')">
            Markets
        </flux:navlist.item>

    </flux:navlist.group>

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
