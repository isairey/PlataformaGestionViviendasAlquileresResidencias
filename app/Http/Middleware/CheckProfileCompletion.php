<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            auth()->user()->role === 'landlord' &&
            ! auth()->user()->profile_completed &&
            ! $request->routeIs('landlord.setup.edit', 'logout')
        ) {
            return redirect()->route('landlord.setup.edit');
        }

        return $next($request);
    }
}
