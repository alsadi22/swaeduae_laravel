<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'event', 'organization']);
        
        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('certificate_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('event', function($eventQuery) use ($request) {
                      $eventQuery->where('title', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        if ($request->has('status') && $request->status) {
            if ($request->status === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'unverified') {
                $query->where('is_verified', false);
            }
        }
        
        $certificates = $query->latest()->paginate(15);
        
        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['user', 'event', 'organization', 'verifier']);
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Revoke the specified certificate.
     */
    public function revoke(Certificate $certificate)
    {
        $certificate->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return redirect()->route('admin.certificates.index')
                        ->with('success', 'Certificate revoked successfully.');
    }
}