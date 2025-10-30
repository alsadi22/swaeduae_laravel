<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $userId = $request->get('user_id');
        $eventId = $request->get('event_id');
        $organizationId = $request->get('organization_id');
        $isPublic = $request->get('is_public');

        $certificates = Certificate::with(['user', 'event', 'organization'])
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($eventId, function ($query, $eventId) {
                return $query->where('event_id', $eventId);
            })
            ->when($organizationId, function ($query, $organizationId) {
                return $query->where('organization_id', $organizationId);
            })
            ->when($isPublic !== null, function ($query) use ($isPublic) {
                return $query->where('is_public', $isPublic);
            })
            ->paginate($perPage);

        return response()->json($certificates);
    }

    /**
     * Display the specified certificate.
     *
     * @param  \App\Models\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function show(Certificate $certificate)
    {
        // Load relationships
        $certificate->load(['user', 'event.organization', 'organization']);
        
        return response()->json($certificate);
    }

    /**
     * Store a newly created certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'event_id' => 'required|exists:events,id',
            'organization_id' => 'required|exists:organizations,id',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'string|max:1000',
            'hours_completed' => 'required|numeric|min:0',
            'event_date' => 'required|date',
            'template' => 'string|max:100',
            'custom_fields' => 'array',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate unique certificate number and verification code
        $certificateData = array_merge(
            $request->only([
                'user_id', 'event_id', 'organization_id', 'type', 'title', 
                'description', 'hours_completed', 'event_date', 'template', 
                'custom_fields', 'is_public'
            ]),
            [
                'certificate_number' => Certificate::generateCertificateNumber(),
                'verification_code' => Certificate::generateVerificationCode(),
                'issued_date' => now(),
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]
        );

        $certificate = Certificate::create($certificateData);

        return response()->json($certificate, 201);
    }

    /**
     * Update the specified certificate in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Certificate $certificate)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string|max:1000',
            'hours_completed' => 'numeric|min:0',
            'template' => 'string|max:100',
            'custom_fields' => 'array',
            'is_public' => 'boolean',
            'is_verified' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If verifying certificate, set verification details
        if ($request->has('is_verified') && $request->is_verified && !$certificate->is_verified) {
            $certificate->update(array_merge(
                $request->only([
                    'title', 'description', 'hours_completed', 'template', 
                    'custom_fields', 'is_public', 'is_verified'
                ]),
                [
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                ]
            ));
        } else {
            $certificate->update($request->only([
                'title', 'description', 'hours_completed', 'template', 
                'custom_fields', 'is_public', 'is_verified'
            ]));
        }

        return response()->json($certificate);
    }

    /**
     * Remove the specified certificate from storage.
     *
     * @param  \App\Models\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return response()->json(['message' => 'Certificate deleted successfully']);
    }

    /**
     * Get certificates for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myCertificates(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $type = $request->get('type');

        $certificates = Auth::user()->certificates()
            ->with(['event.organization', 'organization'])
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->paginate($perPage);

        return response()->json($certificates);
    }

    /**
     * Verify a certificate using its verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $certificate = Certificate::where('verification_code', $request->verification_code)
            ->with(['user', 'event.organization', 'organization'])
            ->first();

        if (!$certificate) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        if (!$certificate->is_verified) {
            return response()->json(['message' => 'Certificate is not verified'], 400);
        }

        return response()->json([
            'valid' => true,
            'certificate' => $certificate,
        ]);
    }

    /**
     * Get public certificates for a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function userPublicCertificates(User $user)
    {
        $certificates = $user->certificates()
            ->where('is_public', true)
            ->with(['event.organization', 'organization'])
            ->paginate(15);

        return response()->json($certificates);
    }
}