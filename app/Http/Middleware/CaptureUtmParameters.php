<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmParameters
{
    public function handle(Request $request, Closure $next): Response
    {
        $keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];
        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $request->session()->put('utm.'.$key, $request->query($key) ?? $request->input($key));
            }
        }

        return $next($request);
    }
}
