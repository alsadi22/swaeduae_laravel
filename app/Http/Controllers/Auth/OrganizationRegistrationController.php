<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class OrganizationRegistrationController extends Controller
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
            // Organization Information
            'name' => ['required', 'string', 'max:255', 'unique:organizations'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:organizations'],
            'phone' => ['required', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'organization_type' => ['required', 'in:ngo,government,private,educational,community'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'emirate' => ['required', 'in:abu_dhabi,dubai,sharjah,ajman,umm_al_quwain,ras_al_khaimah,fujairah'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:2000'],
            'mission_statement' => ['nullable', 'string', 'max:1000'],
            'focus_areas' => ['nullable', 'array'],
            'focus_areas.*' => ['string', 'in:education,healthcare,environment,social_services,youth_development,elderly_care,disability_support,community_development,arts_culture,sports_recreation,emergency_relief,animal_welfare'],
            
            // Contact Person Information
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'contact_position' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Documents
            'documents.trade_license' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
            'documents.emirates_id' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
            'documents.authorization_letter' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
            
            // Terms and Conditions
            'terms' => ['required', 'accepted'],
            'verification_consent' => ['required', 'accepted'],
        ]);

        try {
            // Create the organization
            $organization = Organization::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'organization_type' => $request->organization_type,
                'founded_year' => $request->founded_year,
                'emirate' => $request->emirate,
                'city' => $request->city,
                'address' => $request->address,
                'description' => $request->description,
                'mission_statement' => $request->mission_statement,
                'focus_areas' => $request->focus_areas ?? [],
                'status' => 'pending', // Pending verification
                'is_verified' => false,
                'documents' => $this->uploadDocuments($request),
            ]);

            // Create the contact person user account
            $user = User::create([
                'name' => $request->contact_name,
                'email' => $request->contact_email,
                'phone' => $request->contact_phone,
                'password' => Hash::make($request->password),
                'is_verified' => false,
                'profile_completed' => true, // Organization contact is considered complete
            ]);

            // Assign organization manager role
            $organizationManagerRole = Role::firstOrCreate(['name' => 'organization-manager']);
            $user->assignRole($organizationManagerRole);

            // Associate user with organization
            $organization->users()->attach($user->id, [
                'role' => 'manager',
                'is_active' => true,
                'joined_at' => now(),
            ]);

            // Store additional contact information in user profile
            $user->update([
                'position' => $request->contact_position,
                'organization_id' => $organization->id, // If you have this field
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('organization.dashboard')->with('success', 
                'Organization registration submitted successfully! Your application is under review. You will be notified once verified.');

        } catch (\Exception $e) {
            // Clean up uploaded files if organization creation fails
            if (isset($organization)) {
                $this->cleanupDocuments($organization->documents ?? []);
            }
            
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    /**
     * Upload and store organization documents
     */
    private function uploadDocuments(Request $request): array
    {
        $documents = [];
        $documentTypes = ['trade_license', 'emirates_id', 'authorization_letter'];

        foreach ($documentTypes as $type) {
            if ($request->hasFile("documents.{$type}")) {
                $file = $request->file("documents.{$type}");
                $filename = time() . '_' . $type . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('organization-documents', $filename, 'private');
                
                $documents[$type] = [
                    'original_name' => $file->getClientOriginalName(),
                    'filename' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }

        return $documents;
    }

    /**
     * Clean up uploaded documents
     */
    private function cleanupDocuments(array $documents): void
    {
        foreach ($documents as $document) {
            if (isset($document['path']) && Storage::disk('private')->exists($document['path'])) {
                Storage::disk('private')->delete($document['path']);
            }
        }
    }

    /**
     * Display organization registration success page
     */
    public function success(): View
    {
        return view('auth.organization-registration-success');
    }
}