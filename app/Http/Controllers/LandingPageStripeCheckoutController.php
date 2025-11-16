<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class LandingPageStripeCheckoutController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(config('cashier.secret'));

        $priceId = $request->input('price_id');
        $successUrl = $request->input('success_url', '/thank-you');
        $cancelUrl = $request->input('cancel_url');

        // Validate required fields
        if (empty($priceId)) {
            return back()->withErrors(['error' => 'Invalid product configuration']);
        }

        // Collect all form data except CSRF and internal fields
        $metadata = $request->except(['_token', 'price_id', 'success_url', 'cancel_url']);

        // Build absolute URLs
        $baseUrl = config('app.url');
        $successUrlFull = str_starts_with($successUrl, 'http') ? $successUrl : $baseUrl.$successUrl;
        $cancelUrlFull = $cancelUrl ? (str_starts_with($cancelUrl, 'http') ? $cancelUrl : $baseUrl.$cancelUrl) : $request->headers->get('referer');

        // Add success parameter to success URL
        $successUrlWithParams = $successUrlFull.(parse_url($successUrlFull, PHP_URL_QUERY) ? '&' : '?').'success=true&session_id={CHECKOUT_SESSION_ID}';

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $successUrlWithParams,
                'cancel_url' => $cancelUrlFull,
                'metadata' => $metadata,
                'customer_email' => $metadata['email'] ?? null,
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to create checkout session. Please try again.']);
        }
    }
}
