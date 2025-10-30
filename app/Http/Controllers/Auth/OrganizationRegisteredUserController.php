<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OrganizationRegisteredUserController extends Controller
{
    /**
     * Display the organization registration view.
     */
    public function create(): View
    {
        return view('auth.organization-register');
    }

    /**
     * Handle an incoming organization registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Account Information
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Organization Information
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_phone' => ['required', 'string', 'max:20'],
            'organization_email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'license_number' => ['required', 'string', 'max:100', 'unique:organizations'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'employee_count' => ['nullable', 'integer', 'min:1'],
            
            // Organization Address
            'organization_address' => ['required', 'string', 'max:500'],
            'organization_city' => ['required', 'string', 'max:100'],
            'organization_emirate' => ['required', 'in:abu_dhabi,dubai,sharjah,ajman,umm_al_quwain,ras_al_khaimah,fujairah'],
            'organization_postal_code' => ['nullable', 'string', 'max:20'],
            
            // Primary Contact
            'primary_contact_name' => ['required', 'string', 'max:255'],
            'primary_contact_position' => ['required', 'string', 'max:255'],
            'primary_contact_email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'primary_contact_phone' => ['required', 'string', 'max:20'],
            
            // Terms and Conditions
            'terms' => ['required', 'accepted'],
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Create organization
        $organization = Organization::create([
            'name' => $request->organization_name,
            'phone' => $request->organization_phone,
            'email' => $request->organization_email,
            'license_number' => $request->license_number,
            'website' => $request->website,
            'description' => $request->description,
            'founded_year' => $request->founded_year,
            'employee_count' => $request->employee_count,
            'address' => $request->organization_address,
            'city' => $request->organization_city,
            'emirate' => $request->organization_emirate,
            'postal_code' => $request->organization_postal_code,
            'primary_contact_name' => $request->primary_contact_name,
            'primary_contact_position' => $request->primary_contact_position,
            'primary_contact_email' => $request->primary_contact_email,
            'primary_contact_phone' => $request->primary_contact_phone,
            'status' => 'pending', // Organizations need approval
            'user_id' => $user->id, // Link to the user who registered the organization
        ]);

        // Assign organization role to user
        $user->assignRole('organization');

        // Link user to organization
        $user->organizations()->attach($organization->id, [
            'role' => 'admin',
            'joined_at' => now(),
            'is_active' => true
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to organization dashboard or pending approval page
        return redirect()->route('organizations.pending-approval');
    }
}