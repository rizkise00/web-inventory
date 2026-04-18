<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isApproved()) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is pending approval. Please wait for admin approval.']);
        }

        return $next($request);
    }
}
