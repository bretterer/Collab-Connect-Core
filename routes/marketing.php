<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/waitlist', function (Illuminate\Http\Request $request) {
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

    // Prepare CSV data
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

    return response()->json([
        'success' => true,
        'message' => 'Thank you for joining our waitlist!',
    ]);
})->name('waitlist.store');
