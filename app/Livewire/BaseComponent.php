<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

abstract class BaseComponent extends Component
{
    /**
     * Get the authenticated user safely
     */
    protected function getAuthenticatedUser()
    {
        return Auth::user();
    }

    /**
     * Safe redirect with navigation
     */
    protected function safeRedirect(string $route, bool $navigate = true): void
    {
        $this->redirect(route($route, absolute: false), navigate: $navigate);
    }

    /**
     * Flash success message and redirect
     */
    protected function flashAndRedirect(string $message, string $route, bool $navigate = true): void
    {
        session()->flash('message', $message);
        $this->safeRedirect($route, $navigate);
    }

    /**
     * Flash error message
     */
    protected function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    /**
     * Flash success message
     */
    protected function flashSuccess(string $message): void
    {
        session()->flash('success', $message);
    }

    /**
     * Get validation rules for common fields
     */
    protected function getCommonValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'zip_code' => 'required|string|max:10',
            'url' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Remove empty values from array
     */
    protected function filterEmptyValues(array $array): array
    {
        return array_filter($array, fn($value) => !empty($value));
    }

    /**
     * Reset array keys after filtering
     */
    protected function resetArrayKeys(array $array): array
    {
        return array_values($array);
    }

    /**
     * Add item to array property
     */
    protected function addToArray(string $property, $defaultValue = ''): void
    {
        $currentArray = $this->{$property} ?? [];
        $currentArray[] = $defaultValue;
        $this->{$property} = $currentArray;
    }

    /**
     * Remove item from array property
     */
    protected function removeFromArray(string $property, int $index, int $minCount = 1): void
    {
        $currentArray = $this->{$property} ?? [];

        if (count($currentArray) > $minCount) {
            unset($currentArray[$index]);
            $this->{$property} = $this->resetArrayKeys($currentArray);
        }
    }

    /**
     * Get enum options for forms
     */
    protected function getEnumOptions(string $enumClass, ?string $method = null): array
    {
        if ($method && method_exists($enumClass, $method)) {
            return $enumClass::toOptions($enumClass::$method());
        }

        return $enumClass::toOptions();
    }

    /**
     * Validate enum values
     */
    protected function validateEnumValues(array $values, string $enumClass): bool
    {
        $validValues = array_column($enumClass::cases(), 'value');
        return empty(array_diff($values, $validValues));
    }

    /**
     * Get user's account type safely
     */
    protected function getUserAccountType(): ?\App\Enums\AccountType
    {
        $user = $this->getAuthenticatedUser();
        return $user?->account_type;
    }

    /**
     * Check if user is business account
     */
    protected function isBusinessAccount(): bool
    {
        return $this->getUserAccountType() === \App\Enums\AccountType::BUSINESS;
    }

    /**
     * Check if user is influencer account
     */
    protected function isInfluencerAccount(): bool
    {
        return $this->getUserAccountType() === \App\Enums\AccountType::INFLUENCER;
    }

    /**
     * Check if user is admin account
     */
    protected function isAdminAccount(): bool
    {
        return $this->getUserAccountType() === \App\Enums\AccountType::ADMIN;
    }
}