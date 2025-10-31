<?php

namespace App\Services;

use App\Models\User;
use App\Models\SocialAuthProvider;
use Illuminate\Support\Facades\DB;

class SocialAuthService
{
    /**
     * Handle social authentication
     */
    public function handleSocialCallback($provider, $providerUser)
    {
        $socialAuth = SocialAuthProvider::where('provider', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($socialAuth) {
            // Update existing social auth
            $socialAuth->update([
                'access_token' => $providerUser->token ?? null,
                'refresh_token' => $providerUser->refreshToken ?? null,
                'provider_data' => [
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                    'avatar' => $providerUser->getAvatar(),
                ],
            ]);

            return $socialAuth->user;
        }

        // Check if user exists by email
        $user = User::where('email', $providerUser->getEmail())->first();

        if ($user) {
            // Link social account to existing user
            SocialAuthProvider::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerUser->getId(),
                'provider_data' => [
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                    'avatar' => $providerUser->getAvatar(),
                ],
                'access_token' => $providerUser->token ?? null,
                'refresh_token' => $providerUser->refreshToken ?? null,
            ]);

            return $user;
        }

        // Create new user
        $user = DB::transaction(function () use ($provider, $providerUser) {
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'password' => bcrypt(str()->random(32)),
                'avatar' => $providerUser->getAvatar(),
                $provider . '_id' => $providerUser->getId(),
                'email_verified_at' => now(),
            ]);

            // Create social auth provider record
            SocialAuthProvider::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerUser->getId(),
                'provider_data' => [
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                    'avatar' => $providerUser->getAvatar(),
                ],
                'access_token' => $providerUser->token ?? null,
                'refresh_token' => $providerUser->refreshToken ?? null,
            ]);

            // Assign volunteer role
            $user->assignRole('volunteer');

            return $user;
        });

        return $user;
    }

    /**
     * Link social account to existing user
     */
    public function linkSocialAccount($user, $provider, $providerUser)
    {
        return SocialAuthProvider::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $providerUser->getId(),
            'provider_data' => [
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'avatar' => $providerUser->getAvatar(),
            ],
            'access_token' => $providerUser->token ?? null,
            'refresh_token' => $providerUser->refreshToken ?? null,
        ]);
    }

    /**
     * Unlink social account
     */
    public function unlinkSocialAccount($user, $provider)
    {
        return SocialAuthProvider::where('user_id', $user->id)
            ->where('provider', $provider)
            ->delete();
    }

    /**
     * Get user's linked social accounts
     */
    public function getLinkedAccounts($user)
    {
        return SocialAuthProvider::where('user_id', $user->id)
            ->pluck('provider')
            ->toArray();
    }
}
