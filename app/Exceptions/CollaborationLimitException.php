<?php

namespace App\Exceptions;

use Exception;

class CollaborationLimitException extends Exception
{
    /**
     * The party that has reached their limit ('influencer' or 'business').
     */
    public string $party;

    public function __construct(string $message, string $party, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->party = $party;
    }

    /**
     * Check if the influencer has reached their limit.
     */
    public function isInfluencerLimit(): bool
    {
        return $this->party === 'influencer';
    }

    /**
     * Check if the business has reached their limit.
     */
    public function isBusinessLimit(): bool
    {
        return $this->party === 'business';
    }
}
