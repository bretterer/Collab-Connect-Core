<!-- Step 1: Basic Business Information -->
<div class="space-y-6">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">1</span>
        </div>
        <flux:heading size="xl" class="text-gray-800 dark:text-gray-200">
            Basic Business Information
        </flux:heading>
    </div>

    <!-- Username Section -->
    <div class="mb-2">
        <flux:field>
            <flux:label>Business Username (Optional)</flux:label>
            <flux:input
                wire:model.live.debounce.500ms="username"
                placeholder="mybusiness"
            />
            <flux:error name="username" />
            @if($username && !$errors->has('username'))
                <div class="flex items-center gap-2 mt-2 text-sm text-green-600 dark:text-green-400">
                    <flux:icon.check-circle class="w-4 h-4" />
                    <span class="font-mono">{{ config('app.url') }}/business/{{ $username }}</span> is available!
                </div>
            @else
                <flux:description class="mt-2">Your unique public profile URL will be: <span class="font-mono">{{ config('app.url') }}/business/{{ $username ?: 'your-username' }}</span></flux:description>
            @endif
        </flux:field>
    </div>

    <flux:separator class="my-4" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <flux:field>
            <flux:label>Business Name</flux:label>
            <flux:input
                wire:model="businessName"
                placeholder="Enter your business name"
            />
            <flux:error name="businessName" />
        </flux:field>

        <flux:field>
            <flux:label>Business Email</flux:label>
            <flux:input
                type="email"
                wire:model="businessEmail"
                placeholder="business@example.com"
            />
            <flux:error name="businessEmail" />
        </flux:field>

        <flux:field>
            <flux:label>Phone Number</flux:label>
            <flux:input
                type="tel"
                wire:model="phoneNumber"
                placeholder="+1 (555) 123-4567"
            />
            <flux:error name="phoneNumber" />
        </flux:field>

        <flux:field>
            <flux:label>Business Website</flux:label>
            <flux:input
                type="url"
                wire:model="website"
                placeholder="https://yourwebsite.com"
            />
            <flux:error name="website" />
        </flux:field>
    </div>

    <!-- Contact Information -->
    <div class="mt-6">
        <flux:heading class="text-gray-800 dark:text-gray-200 mb-4">
            Contact Information
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label>Primary Contact Name</flux:label>
                <flux:input
                    wire:model="contactName"
                    placeholder="Enter primary contact name"
                />
                <flux:error name="contactName" />
            </flux:field>

            <flux:field>
                <flux:label>Role/Title</flux:label>
                <flux:select wire:model="contactRole" placeholder="Select your role">
                    @foreach(\App\Enums\ContactRole::cases() as $role)
                        <flux:select.option value="{{ $role->value }}">{{ $role->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="contactRole" />
            </flux:field>
        </div>
    </div>

    <!-- Business Basics -->
    <div class="mt-6">
        <flux:heading class="text-gray-800 dark:text-gray-200 mb-4">
            Business Basics
        </flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label>Years in Business</flux:label>
                <flux:select wire:model="yearsInBusiness" placeholder="How long have you been in business?">
                    @foreach(\App\Enums\YearsInBusiness::cases() as $years)
                        <flux:select.option value="{{ $years->value }}">{{ $years->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="yearsInBusiness" />
            </flux:field>

            <flux:field>
                <flux:label>Company Size</flux:label>
                <flux:select wire:model="companySize" placeholder="Select company size">
                    @foreach(\App\Enums\CompanySize::cases() as $size)
                        <flux:select.option value="{{ $size->value }}">{{ $size->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="companySize" />
            </flux:field>
        </div>
    </div>
</div>