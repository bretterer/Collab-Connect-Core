<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
            'subject' => $validated['subject']
        ]);

        return back()->with('error', 'Sorry, there was an issue sending your message. Please try again later or contact us directly at hello@collabconnect.app.');
    }
})->name('contact.store');

Route::post('/waitlist', function (Illuminate\Http\Request $request) {
    try {
        // Validate the form data
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'user_type' => 'required|in:business,influencer',
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

    // Prepare CSV data - keeping existing CSV functionality
    $csvData = [
        now()->toDateTimeString(),
        $validated['name'],
        $validated['email'],
        $validated['user_type'],
        $validated['business_name'] ?? '',
        $validated['follower_count'] ?? '',
    ];

    // Define CSV file path
    $csvPath = storage_path('app/private/waitlist.csv');

    // Create directory if it doesn't exist
    if (! file_exists(dirname($csvPath))) {
        mkdir(dirname($csvPath), 0755, true);
    }

    // Check if file exists to determine if we need headers
    $fileExists = file_exists($csvPath);

    // Open file for appending
    $file = fopen($csvPath, 'a');

    // Add headers if file is new
    if (! $fileExists) {
        fputcsv($file, ['Timestamp', 'Name', 'Email', 'User Type', 'Business Name', 'Follower Count']);
    }

    // Add the data
    fputcsv($file, $csvData);
    fclose($file);

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
            'user_type' => $validated['user_type']
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
            'request' => $request->all()
        ]);

        return back()->with('error', 'Sorry, there was an issue processing your signup. Please try again later.');
    }
})->name('waitlist.store');
