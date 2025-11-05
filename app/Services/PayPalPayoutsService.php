<?php

namespace App\Services;

use App\Models\ReferralEnrollment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalPayoutsService
{
    protected string $baseUrl;

    protected string $clientId;

    protected string $secretKey;

    protected ?string $accessToken = null;

    protected ?int $tokenExpiresAt = null;

    /**
     * Create a new service instance.
     */
    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secretKey = config('services.paypal.secret_key');

        // Determine if we're using sandbox or production
        $isSandbox = config('services.paypal.mode', 'sandbox') === 'sandbox';
        $this->baseUrl = $isSandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    /**
     * Get an authenticated HTTP client with access token.
     */
    protected function getClient(): PendingRequest
    {
        $this->ensureAccessToken();

        return Http::withHeaders([
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    /**
     * Ensure we have a valid access token.
     */
    protected function ensureAccessToken(): void
    {
        if ($this->accessToken && $this->tokenExpiresAt && time() < $this->tokenExpiresAt) {
            return;
        }

        $this->refreshAccessToken();
    }

    /**
     * Get a new access token from PayPal.
     */
    protected function refreshAccessToken(): void
    {
        $response = Http::withBasicAuth($this->clientId, $this->secretKey)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            Log::error('PayPal access token request failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new \Exception('Failed to obtain PayPal access token');
        }

        $data = $response->json();
        $this->accessToken = $data['access_token'];
        $this->tokenExpiresAt = time() + ($data['expires_in'] - 60); // Refresh 60s before expiry
    }

    /**
     * Verify a PayPal email address format.
     *
     * Note: PayPal doesn't provide a direct email verification API endpoint without
     * creating actual transactions. This method performs strict email validation.
     * The actual verification that the PayPal account exists and can receive payouts
     * will happen during the first real payout attempt.
     *
     * @param  string  $email  The PayPal email to verify
     * @return array|null Returns payer info if valid format, null if invalid
     */
    public function verifyPayPalEmail(string $email): ?array
    {
        try {
            // Validate email format using PHP's built-in validator
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::warning('PayPal email verification failed: Invalid email format', [
                    'email' => $email,
                ]);

                return null;
            }

            // Additional strict email format checks
            // PayPal emails must be properly formatted and within length limits
            if (strlen($email) > 254 || strlen($email) < 6) {
                Log::warning('PayPal email verification failed: Email length invalid', [
                    'email' => $email,
                    'length' => strlen($email),
                ]);

                return null;
            }

            // Validate email structure with regex
            // Must have valid characters, domain, and TLD
            if (! preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                Log::warning('PayPal email verification failed: Email pattern invalid', [
                    'email' => $email,
                ]);

                return null;
            }

            // Check for common temporary/disposable email domains
            $disposableDomains = [
                'tempmail.com', 'throwaway.email', 'guerrillamail.com',
                '10minutemail.com', 'mailinator.com', 'trashmail.com',
            ];

            $domain = substr(strrchr($email, '@'), 1);
            if (in_array(strtolower($domain), $disposableDomains)) {
                Log::warning('PayPal email verification failed: Disposable email domain', [
                    'email' => $email,
                    'domain' => $domain,
                ]);

                return null;
            }

            // Email format is valid
            // The actual PayPal account verification will happen on first payout
            Log::info('PayPal email format validated successfully', [
                'email' => $email,
            ]);

            return [
                'email' => $email,
                'verified' => true,
                'payer_id' => null, // Will be populated on first successful payout
                'validation_method' => 'format_check',
            ];
        } catch (\Exception $e) {
            Log::error('PayPal email verification exception', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Link a PayPal account to a referral enrollment.
     *
     * @param  ReferralEnrollment  $enrollment  The referral enrollment
     * @param  string  $email  The PayPal email address
     */
    public function linkPayPalAccount(ReferralEnrollment $enrollment, string $email): bool
    {
        $verificationResult = $this->verifyPayPalEmail($email);

        if (! $verificationResult) {
            return false;
        }

        $enrollment->update([
            'paypal_email' => $email,
            'paypal_verified' => $verificationResult['verified'],
            'paypal_connected_at' => now(),
        ]);

        return true;
    }

    /**
     * Create a payout to a referral enrollment.
     *
     * This is where the actual PayPal account verification happens.
     * If the email doesn't exist or can't receive payments, PayPal will return an error.
     *
     * @param  ReferralEnrollment  $enrollment  The referral enrollment
     * @param  float  $amount  The amount to payout in USD
     * @param  string  $note  Optional note for the payout
     * @return array|null Returns payout data if successful, null if failed
     *
     * @throws \Exception If PayPal account not connected
     */
    public function createPayout(ReferralEnrollment $enrollment, float $amount, string $note = 'Referral Commission'): ?array
    {
        if (! $enrollment->hasPayPalConnected()) {
            throw new \Exception('PayPal account not connected or verified');
        }

        try {
            $response = $this->getClient()->post('/v1/payments/payouts', [
                'sender_batch_header' => [
                    'sender_batch_id' => 'Referral_'.time().'_'.$enrollment->id,
                    'email_subject' => 'You have a referral commission payment!',
                    'email_message' => $note,
                ],
                'items' => [
                    [
                        'recipient_type' => 'EMAIL',
                        'amount' => [
                            'value' => number_format($amount, 2, '.', ''),
                            'currency' => 'USD',
                        ],
                        'receiver' => $enrollment->paypal_email,
                        'note' => $note,
                        'sender_item_id' => "REFCOMM_{$enrollment->id}_".time(),
                    ],
                ],
            ]);

            if ($response->failed()) {
                $error = $response->json();
                $errorName = $error['name'] ?? 'UNKNOWN';
                $errorMessage = $error['message'] ?? 'Unknown error';

                // Handle specific PayPal errors
                if (in_array($errorName, ['RECEIVER_UNREGISTERED', 'INVALID_RECEIVER'])) {
                    Log::error('PayPal payout failed: Invalid PayPal account', [
                        'enrollment_id' => $enrollment->id,
                        'email' => $enrollment->paypal_email,
                        'error' => $errorName,
                        'message' => $errorMessage,
                    ]);

                    // Mark the PayPal account as unverified
                    $enrollment->update([
                        'paypal_verified' => false,
                        'paypal_metadata' => array_merge($enrollment->paypal_metadata ?? [], [
                            'last_payout_error' => $errorMessage,
                            'last_payout_error_at' => now()->toIso8601String(),
                        ]),
                    ]);
                } else {
                    Log::error('PayPal payout failed', [
                        'enrollment_id' => $enrollment->id,
                        'status' => $response->status(),
                        'error' => $error,
                    ]);
                }

                return null;
            }

            $data = $response->json();

            // Update metadata with payout information
            $metadata = $enrollment->paypal_metadata ?? [];
            $metadata['last_payout_at'] = now()->toIso8601String();
            $metadata['last_payout_batch_id'] = $data['batch_header']['payout_batch_id'] ?? null;
            $metadata['first_payout_verified'] = true; // Mark that we've verified the account works

            $enrollment->update(['paypal_metadata' => $metadata]);

            return $data;
        } catch (\Exception $e) {
            Log::error('PayPal payout exception', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get payout status by batch ID.
     *
     * @param  string  $batchId  The PayPal batch ID
     */
    public function getPayoutStatus(string $batchId): ?array
    {
        try {
            $response = $this->getClient()->get("/v1/payments/payouts/{$batchId}");

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to get payout status', [
                'batch_id' => $batchId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Disconnect PayPal account from enrollment.
     */
    public function disconnectPayPalAccount(ReferralEnrollment $enrollment): bool
    {
        try {
            $enrollment->disconnectPayPal();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to disconnect PayPal account', [
                'enrollment_id' => $enrollment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
