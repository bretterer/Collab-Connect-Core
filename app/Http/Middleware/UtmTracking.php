<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class UtmTracking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $utm = $request->only([
            'ref',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_term',
            'utm_content',
        ]);

        if (empty($utm)) {
            $cookie = Cookie::get('utm_data');
            if ($cookie) {
                $utm = json_decode($cookie, true);
            }
        }

        if (Auth::check() && !empty($utm)) {
            Cookie::queue(Cookie::forget('utm_data'));

            if (empty(Auth::user()->utm_data)) {
                Auth::user()->update(['utm_data' => $utm]);
            }

        } elseif (!empty($utm)) {
            Cookie::queue('utm_data', json_encode($utm), 60 * 24 * 365);
        }

        return $next($request);
    }
}
