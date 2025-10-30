<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
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
            'date_of_birth' => ['required', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Personal Information
            'gender' => ['required', 'in:male,female'],
            'nationality' => ['required', 'string', 'max:100'],
            'emirates_id' => ['nullable', 'string', 'max:20', 'unique:'.User::class],
            'emirate' => ['required', 'in:abu_dhabi,dubai,sharjah,ajman,umm_al_quwain,ras_al_khaimah,fujairah'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'education_level' => ['nullable', 'in:high_school,diploma,bachelor,master,phd'],
            
            // Volunteer Preferences
            'skills' => ['nullable', 'string', 'max:1000'],
            'interests' => ['nullable', 'string', 'max:1000'],
            'languages' => ['nullable', 'string', 'max:255'],
            'has_transportation' => ['nullable', 'boolean'],
            'bio' => ['nullable', 'string', 'max:2000'],
            
            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            
            // Terms and Conditions
            'terms' => ['required', 'accepted'],
        ]);

        $user = User::create([
            // Account Information
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            
            // Personal Information
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'emirates_id' => $request->emirates_id,
            'emirate' => $request->emirate,
            'city' => $request->city,
            'address' => $request->address,
            'education_level' => $request->education_level,
            
            // Volunteer Preferences
            'skills' => $request->skills,
            'interests' => $request->interests,
            'languages' => $request->languages,
            'has_transportation' => $request->has_transportation,
            'bio' => $request->bio,
            
            // Emergency Contact
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            
            // Initialize volunteer stats
            'total_volunteer_hours' => 0,
            'total_events_attended' => 0,
            'points' => 0,
        ]);

        // Assign default volunteer role
        $user->assignRole('volunteer');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
