<?php

namespace App\Rules;

use App\Models\Business;
use App\Models\Influencer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueUsername implements ValidationRule
{
    protected $ignoreBusinessId;

    protected $ignoreInfluencerId;

    public function __construct($ignoreBusinessId = null, $ignoreInfluencerId = null)
    {
        $this->ignoreBusinessId = $ignoreBusinessId;
        $this->ignoreInfluencerId = $ignoreInfluencerId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        // Check if username exists in businesses table
        $businessExists = Business::where('username', $value)
            ->when($this->ignoreBusinessId, function ($query) {
                $query->where('id', '!=', $this->ignoreBusinessId);
            })
            ->exists();

        if ($businessExists) {
            $fail('This username is already taken.');

            return;
        }

        // Check if username exists in influencers table
        $influencerExists = Influencer::where('username', $value)
            ->when($this->ignoreInfluencerId, function ($query) {
                $query->where('id', '!=', $this->ignoreInfluencerId);
            })
            ->exists();

        if ($influencerExists) {
            $fail('This username is already taken.');

            return;
        }
    }
}
