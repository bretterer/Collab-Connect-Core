<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class DatafastPaymentAPI implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            $invoice = $event->payload['data']['object'];

            // Extract payment details from the invoice
            $paymentData = [
                'amount' => $invoice['amount_paid'] / 100, // Convert from cents to dollars
                'currency' => strtoupper($invoice['currency']),
                'transaction_id' => $invoice['payment_intent'] ?? $invoice['id'],
                'email' => $invoice['customer_email'] ?? null,
                'customer_id' => $invoice['customer'] ?? null,
                'renewal' => $invoice['billing_reason'] === 'subscription_cycle',
                'timestamp' => date('c', $invoice['created']),
            ];

            // Add Datafast tracking IDs from subscription metadata if available
            if (isset($invoice['subscription_details']['metadata'])) {
                $metadata = $invoice['subscription_details']['metadata'];

                if (! empty($metadata['datafast_visitor_id'])) {
                    $paymentData['datafast_visitor_id'] = $metadata['datafast_visitor_id'];
                }

                if (! empty($metadata['datafast_session_id'])) {
                    $paymentData['datafast_session_id'] = $metadata['datafast_session_id'];
                }
            }

            // Make API call to Datafast
            try {
                $response = Http::withToken(config('services.datafast.api_key'))
                    ->post('https://datafa.st/api/v1/payments', $paymentData);

                if ($response->successful()) {
                    Log::info('Datafast payment recorded successfully', [
                        'transaction_id' => $paymentData['transaction_id'],
                        'response' => $response->json(),
                    ]);
                } else {
                    Log::error('Datafast payment API failed', [
                        'transaction_id' => $paymentData['transaction_id'],
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Datafast payment API exception', [
                    'transaction_id' => $paymentData['transaction_id'],
                    'error' => $e->getMessage(),
                ]);

                // Re-throw to allow retry via queue
                throw $e;
            }
        }
    }
}
