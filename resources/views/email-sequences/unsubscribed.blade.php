<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 dark:bg-zinc-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <flux:card class="text-center">
                <div class="mb-6">
                    <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <flux:icon.check class="size-8 text-green-600 dark:text-green-400" />
                    </div>
                </div>

                <flux:heading size="xl" class="mb-4">
                    You've Been Unsubscribed
                </flux:heading>

                <flux:text class="mb-6">
                    {{ $subscriber->email }} has been successfully removed from our email list.
                </flux:text>

                <div class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">
                    Changed your mind? Contact us to resubscribe.
                </div>

                <flux:button href="{{ url('/') }}" variant="primary">
                    Return to Home
                </flux:button>
            </flux:card>
        </div>
    </div>
</body>
</html>
