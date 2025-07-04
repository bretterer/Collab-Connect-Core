<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/waitlist', function (Illuminate\Http\Request $request) {
    // Validate the form data
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'user_type' => 'required|in:business,influencer',
    ]);

    // Prepare CSV data
    $csvData = [
        now()->toDateTimeString(),
        $validated['name'],
        $validated['email'],
        $validated['user_type'],
    ];

    // Define CSV file path
    $csvPath = storage_path('app/private/waitlist.csv');

    // Create directory if it doesn't exist
    if (!file_exists(dirname($csvPath))) {
        mkdir(dirname($csvPath), 0755, true);
    }

    // Check if file exists to determine if we need headers
    $fileExists = file_exists($csvPath);

    // Open file for appending
    $file = fopen($csvPath, 'a');

    // Add headers if file is new
    if (!$fileExists) {
        fputcsv($file, ['Timestamp', 'Name', 'Email', 'User Type']);
    }

    // Add the data
    fputcsv($file, $csvData);
    fclose($file);

    return response()->json([
        'success' => true,
        'message' => 'Thank you for joining our waitlist!'
    ]);
})->name('waitlist.store');