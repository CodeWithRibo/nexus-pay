<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && strtolower((string) auth()->user()->role) === 'admin') {
            return $next($request);
        }
        abort(403, 'Unauthorized action');
    }
}
