<div class="space-y-8">

    @includewhen(auth()->user()->account_type === App\Enums\AccountType::BUSINESS, 'livewire.dashboard.business')

    @includewhen(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER, 'livewire.dashboard.influencer')

    @if(auth()->user()->account_type !== App\Enums\AccountType::BUSINESS && auth()->user()->account_type !== App\Enums\AccountType::INFLUENCER)
        <!-- DEFAULT DASHBOARD -->
        <div class="text-center py-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Welcome to CollabConnect, {{ auth()->user()->name }}!
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                Your account setup is complete and you're ready to start collaborating.
            </p>
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                Get Started
            </div>
        </div>
    @endif
</div>