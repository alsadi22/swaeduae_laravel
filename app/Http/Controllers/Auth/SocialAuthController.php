<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    /**
     * Redirect to OAuth provider
     */
    public function redirectToProvider($provider)
    {
        $this->validateProvider($provider);
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth callback
     */
    public function handleProviderCallback($provider)
    {
        $this->validateProvider($provider);

        try {
            $providerUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to authenticate with ' . ucfirst($provider));
        }

        $user = $this->socialAuthService->handleSocialCallback($provider, $providerUser);

        Auth::login($user, remember: true);

        // Redirect based on role
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole(['organization-manager', 'organization-staff'])) {
            return redirect()->route('organization.dashboard');
        }

        return redirect()->route('volunteer.dashboard');
    }

    /**
     * Link social account to existing user
     */
    public function linkAccount($provider)
    {
        $this->validateProvider($provider);
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Unlink social account
     */
    public function unlinkAccount($provider)
    {
        $this->validateProvider($provider);
        $this->socialAuthService->unlinkSocialAccount(auth()->user(), $provider);
        return back()->with('success', ucfirst($provider) . ' account unlinked successfully');
    }

    /**
     * Validate provider
     */
    private function validateProvider($provider)
    {
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            abort(404);
        }
    }
}
