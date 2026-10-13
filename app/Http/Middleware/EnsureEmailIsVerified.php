<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user &&
            !$user->email_verified_at &&
            !$request->routeIs('email.verification*')
        ) {
            return redirect()->route('email.verification');
        }

        return $next($request);
    }
}
