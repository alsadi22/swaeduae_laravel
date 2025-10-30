<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has any of the required permissions
        if (!$user->hasAnyPermission(...$permissions)) {
            // If user doesn't have required permission, redirect based on their role
            if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this resource.');
            } elseif ($user->hasAnyRole('organization-manager', 'organization-staff')) {
                return redirect()->route('organization.dashboard')->with('error', 'You do not have permission to access this resource.');
            } elseif ($user->hasRole('volunteer')) {
                return redirect()->route('volunteer.dashboard')->with('error', 'You do not have permission to access this resource.');
            } else {
                // Default redirect for users without specific roles
                return redirect()->route('dashboard')->with('error', 'You do not have permission to access this resource.');
            }
        }

        return $next($request);
    }
}
