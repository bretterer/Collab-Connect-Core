<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DataFastProxyController extends Controller
{
    public function script()
    {
        // Cache the script for 1 year
        return Cache::remember('datafast_script', 31536000, function () {
            $response = Http::get('https://datafa.st/js/script.js');

            return response($response->body(), 200, [
                'Content-Type' => 'application/javascript',
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        });
    }

    public function events(Request $request)
    {
        $response = Http::withHeaders([
            'User-Agent' => $request->header('User-Agent'),
            'Content-Type' => 'application/json',
        ])->post('https://datafa.st/api/events', $request->all());

        return response($response->body(), $response->status());
    }
}
