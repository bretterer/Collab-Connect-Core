<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Masmerise\Toaster\Toaster;
use Symfony\Component\HttpFoundation\Response;

class HandleToastMessages
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Handle session flash toast messages
        if (session()->has('toast')) {
            $toastData = session('toast');
            \Log::info('Toast data found: ', $toastData);
            
            if (is_array($toastData) && isset($toastData['message']) && isset($toastData['type'])) {
                match ($toastData['type']) {
                    'success' => Toaster::success($toastData['message']),
                    'error' => Toaster::error($toastData['message']),
                    'warning' => Toaster::warning($toastData['message']),
                    'info' => Toaster::info($toastData['message']),
                    default => Toaster::info($toastData['message'])
                };
            }
            
            session()->forget('toast');
        }

        return $response;
    }
}