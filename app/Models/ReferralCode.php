<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    public function generateCode(): string
    {
        return strtoupper(bin2hex(random_bytes(4))); // Generates a random 8-character code
    }
}
