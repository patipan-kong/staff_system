<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsStaff
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isStaff()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            return redirect()->route('login')->with('error', 'You must be logged in to access this resource.');
        }

        return $next($request);
    }
}