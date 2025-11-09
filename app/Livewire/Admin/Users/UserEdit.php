<?php

namespace App\Livewire\Admin\Users;

use App\Enums\AccountType;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserEdit extends Component
{
    public User $user;

    public string $name;

    public string $email;

    public int $accountType;

    public bool $allowAdminAccess = false;

    public string $activeTab = 'account';

    public string $featureSearch = '';

    public string $featureStatusFilter = '';

    public array $features = [];

    protected $queryString = [
        'activeTab' => ['except' => 'account'],
    ];

    public function mount(User $user)
    {
        $this->user = $user->load(['currentBusiness', 'influencer', 'socialMediaAccounts']);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->accountType = $user->account_type->value;
        $this->allowAdminAccess = $user->access_admin;

        // Load current feature flag states
        $this->loadFeatureStates();
    }

    public function loadFeatureStates()
    {
        foreach ($this->getAvailableFeatures() as $feature) {
            $this->features[$feature->key] = $this->isFeatureEnabled($feature->key);
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->user->id,
            'accountType' => 'required|'.AccountType::validationRule(),
        ];
    }

    public function save()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'account_type' => AccountType::from($this->accountType),
            'access_admin' => $this->allowAdminAccess,
        ]);

        Flux::toast('User updated successfully.');

        return redirect()->route('admin.users.show', $this->user);
    }

    public function toggleFeature(string $featureKey)
    {
        $isEnabled = $this->features[$featureKey] ?? false;

        if ($isEnabled) {
            Feature::for($this->user)->activate($featureKey);
            Flux::toast("Feature '{$featureKey}' enabled for user.");
        } else {
            Feature::for($this->user)->deactivate($featureKey);
            Flux::toast("Feature '{$featureKey}' disabled for user.");
        }
    }

    public function isFeatureEnabled(string $featureKey): bool
    {
        return Feature::for($this->user)->active($featureKey);
    }

    public function getAvailableFeatures(): array
    {
        $storedFlags = Feature::defined();

        $featureFlags = [];
        foreach ($storedFlags as $flag) {
            $flagClass = new $flag;
            $featureFlags[$flag] = $flagClass;
        }

        return $featureFlags;
    }

    protected function getFeatureDescription(string $featureClass): string
    {
        // Try to get description from class docblock or property
        try {
            $reflection = new \ReflectionClass($featureClass);
            $docComment = $reflection->getDocComment();

            if ($docComment && preg_match('/@description\s+(.+)/', $docComment, $matches)) {
                return trim($matches[1]);
            }

            // Check if the class has a public description property
            if (property_exists($featureClass, 'description')) {
                return (new $featureClass)->description;
            }
        } catch (\Exception $e) {
            // Silently fail and use default
        }

        return 'No description available.';
    }

    public function getFilteredFeatures(): array
    {
        $features = $this->getAvailableFeatures();

        // Apply search filter
        if ($this->featureSearch) {
            $search = strtolower($this->featureSearch);
            $features = array_filter($features, function ($feature) use ($search) {
                return str_contains(strtolower($feature['name']), $search) ||
                       str_contains(strtolower($feature['description'] ?? ''), $search) ||
                       str_contains(strtolower($feature['key']), $search);
            });
        }

        // Apply status filter
        if ($this->featureStatusFilter) {
            $features = array_filter($features, function ($feature) {
                $isEnabled = $this->isFeatureEnabled($feature['key']);

                return $this->featureStatusFilter === 'enabled' ? $isEnabled : ! $isEnabled;
            });
        }

        return array_values($features);
    }

    public function getAccountTypeOptions()
    {
        return collect(AccountType::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.users.user-edit');
    }
}
