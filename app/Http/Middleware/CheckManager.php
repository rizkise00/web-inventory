<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckManager
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        return $next($request);
    }
}
