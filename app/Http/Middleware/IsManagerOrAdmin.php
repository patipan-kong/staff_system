<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsManagerOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isManagerOrAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}