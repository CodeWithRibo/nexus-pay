<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isCashierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'cashier') {
            return $next($request);
        }
        abort(403, 'Unauthorized action');
    }
}
