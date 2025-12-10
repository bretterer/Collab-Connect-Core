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

    <flux:navlist.item href="{{ route('admin.beta-invites') }}" icon="envelope" :current="request()->routeIs('admin.beta-invites')">
        Beta Invites
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.feedback') }}" icon="chat-bubble-left" :current="request()->routeIs('admin.feedback')">
        Feedback
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.referrals.index') }}" icon="user-group" :current="request()->routeIs('admin.referrals.*')">
        Referrals
    </flux:navlist.item>

    <flux:navlist.item href="{{ route('admin.custom-signup-pages.index') }}" icon="user-plus" :current="request()->routeIs('admin.custom-signup-pages.*')">
        Custom Signup Pages
    </flux:navlist.item>

    <flux:navlist.group heading="Marketing" expandable :expanded="request()->routeIs('admin.marketing.*')">
        <flux:navlist.item href="{{ route('admin.marketing.landing-pages.index') }}" icon="document" :current="request()->routeIs('admin.marketing.landing-pages.*')">
            Landing Pages
        </flux:navlist.item>
        <flux:navlist.item href="{{ route('admin.marketing.forms.index') }}" icon="clipboard-document-list" :current="request()->routeIs('admin.marketing.forms.*')">
            Forms
        </flux:navlist.item>
        <flux:navlist.item href="{{ route('admin.marketing.email-sequences.index') }}" icon="envelope" :current="request()->routeIs('admin.marketing.email-sequences.*')">
            Email Sequences
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
