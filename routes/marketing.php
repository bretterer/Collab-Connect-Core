<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', function () {
    if (Cookie::has('referral_code')) {
        $referralCode = Cookie::get('referral_code');
    }

    return view('welcome', [
        'referralCode' => $referralCode ?? null,
    ]);
})->name('home');

// Landing Pages
Route::get('/l/{slug}', [App\Http\Controllers\LandingPageController::class, 'show'])->name('landing.show');
Route::post('/landing/stripe-checkout', [App\Http\Controllers\LandingPageStripeCheckoutController::class, 'createCheckoutSession'])->name('landing.stripe-checkout');
Route::get('/thank-you', fn () => view('thank-you'))->name('thank-you');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/careers', function () {
    return view('careers');
})->name('careers');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/landing', function () {
    return view('landing');
})->name('landing');

Route::get('/landing/thank-you', function () {
    return view('landing-thank-you');
})->name('landing.thank-you');

Route::post('/landing/signup', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
    ]);

    try {
        // Store the email signup (you can create a model for this later)
        // For now, we'll just send a notification email
        \Illuminate\Support\Facades\Mail::to('hello@collabconnect.app')
            ->send(new \App\Mail\LandingPageSignup(
                name: $validated['name'],
                email: $validated['email']
            ));

        return redirect()->route('landing.thank-you');
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Landing page signup failed', [
            'error' => $e->getMessage(),
            'email' => $validated['email'],
        ]);

        return back()->with('error', 'Sorry, there was an issue processing your request. Please try again later.');
    }
})->name('landing.signup');

Route::get('/refer', function (Request $request) {
    if ($request->has('code')) {
        $code = $request->input('code');
        Cookie::queue('referral_code', $code, 60 * 24 * 30); // Store for 30 days
    }

    return redirect()->route('home');
});

Route::post('/contact', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|in:general,business,influencer,technical,beta,media,other',
        'message' => 'required|string|max:2000',
        'newsletter' => 'nullable|boolean',
    ]);

    try {
        // Send email to CollabConnect team
        \Illuminate\Support\Facades\Mail::to('hello@collabconnect.app')
            ->send(new \App\Mail\ContactInquiry(
                firstName: $validated['first_name'],
                lastName: $validated['last_name'],
                email: $validated['email'],
                inquirySubject: $validated['subject'],
                message: $validated['message'],
                newsletter: $validated['newsletter'] ?? false
            ));

        // Send confirmation email to the user
        \Illuminate\Support\Facades\Mail::to($validated['email'])
            ->send(new \App\Mail\ContactConfirmation(
                firstName: $validated['first_name'],
                lastName: $validated['last_name'],
                inquirySubject: $validated['subject']
            ));

        $responseDays = config('collabconnect.support_response_days');
        $responseText = $responseDays == 1 ? '1 business day' : "{$responseDays} business days";

        // Clear any old input data and redirect with success message
        $request->session()->forget('_old_input');

        return back()->with('success', "Thank you for contacting us! We've sent you a confirmation email and will get back to you within {$responseText}.");
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Contact form email failed', [
            'error' => $e->getMessage(),
            'email' => $validated['email'],
            'subject' => $validated['subject'],
        ]);

        return back()->with('error', 'Sorry, there was an issue sending your message. Please try again later or contact us directly at hello@collabconnect.app.');
    }
})->name('contact.store')->middleware(ProtectAgainstSpam::class);

Route::post('/waitlist', function (Illuminate\Http\Request $request) {
    try {
        // Validate the form data
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'user_type' => 'required|in:business,influencer',
            'referral_code' => 'nullable|string|max:8',
        ];

        // Add conditional validation based on user type
        if ($request->input('user_type') === 'business') {
            $rules['business_name'] = 'required|string|max:255';
            $rules['follower_count'] = 'nullable'; // Not needed for businesses
        } elseif ($request->input('user_type') === 'influencer') {
            $rules['follower_count'] = 'required|string|in:1K-5K,5K-15K,15K-50K,50K-100K,100K+';
            $rules['business_name'] = 'nullable'; // Not needed for influencers
        } else {
            $rules['business_name'] = 'nullable|string|max:255';
            $rules['follower_count'] = 'nullable|string|in:1K-5K,5K-15K,15K-50K,50K-100K,100K+';
        }

        $validated = $request->validate($rules);

        // Save to database using Waitlist model
        \App\Models\Waitlist::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_type' => $validated['user_type'],
            'referral_code' => $validated['referral_code'] ?? '',
            'business_name' => $validated['business_name'] ?? '',
            'follower_count' => $validated['follower_count'] ?? '',
        ]);

        try {
            // Send notification email to CollabConnect team
            \Illuminate\Support\Facades\Mail::to('hello@collabconnect.app')
                ->send(new \App\Mail\BetaSignupNotification(
                    name: $validated['name'],
                    email: $validated['email'],
                    userType: $validated['user_type'],
                    businessName: $validated['business_name'] ?? null,
                    followerCount: $validated['follower_count'] ?? null
                ));

            // Send confirmation email to the user
            \Illuminate\Support\Facades\Mail::to($validated['email'])
                ->send(new \App\Mail\BetaSignupConfirmation(
                    name: $validated['name'],
                    userType: $validated['user_type'],
                    businessName: $validated['business_name'] ?? null,
                    followerCount: $validated['follower_count'] ?? null
                ));

            return back()->with('success', 'Thank you for joining our beta waitlist! Check your email for confirmation details.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Beta signup email failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
                'user_type' => $validated['user_type'],
            ]);

            // Still return success since CSV was saved, but mention email issue
            return back()->with('success', 'Thank you for joining our beta waitlist! We\'ve received your signup and will be in touch soon.');
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors properly
        return back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        // Handle any other errors
        \Illuminate\Support\Facades\Log::error('Beta signup failed', [
            'error' => $e->getMessage(),
            'request' => $request->all(),
        ]);

        return back()->with('error', 'Sorry, there was an issue processing your signup. Please try again later.');
    }
})->name('waitlist.store');
