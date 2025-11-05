<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Review Monthly Payouts</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Approve, edit, and process referral payouts for {{ now()->month($selectedMonth)->format('F Y') }}.</p>
    </div>

    <flux:card>
        <flux:heading size="lg">Placeholder: Payout Review Coming Soon</flux:heading>
        <flux:text class="mt-2">
            This page will include:
            - Month/year selector
            - Summary stats
            - Payout review table with approve/deny actions
            - Bulk approval functionality
            - Expandable rows showing referred accounts
        </flux:text>
    </flux:card>
</div>
