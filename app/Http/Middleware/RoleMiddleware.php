<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has any of the required roles
        if (!$user->hasAnyRole(...$roles)) {
            // If user doesn't have required role, redirect based on their actual role
            if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasAnyRole('organization-manager', 'organization-staff')) {
                return redirect()->route('organization.dashboard');
            } elseif ($user->hasRole('volunteer')) {
                return redirect()->route('volunteer.dashboard');
            } else {
                // Default redirect for users without specific roles
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
