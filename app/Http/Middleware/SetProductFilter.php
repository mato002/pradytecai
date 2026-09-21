<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetProductFilter
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('product_filter')) {
            $value = $request->input('product_filter');
            if ($value === null || $value === '' || $value === 'all') {
                $request->session()->forget('marketing.product_id');
            } else {
                $request->session()->put('marketing.product_id', (int) $value);
            }
        }

        return $next($request);
    }
}
