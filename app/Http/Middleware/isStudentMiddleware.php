<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isStudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'student') {
            return $next($request);
        }
        abort(403, 'Unauthorized action');
    }
}
