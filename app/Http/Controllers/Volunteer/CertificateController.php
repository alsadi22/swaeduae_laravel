<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Display a listing of the user's certificates.
     */
    public function index()
    {
        $user = Auth::user();
        
        $certificates = Certificate::with(['event', 'organization'])
            ->where('user_id', $user->id)
            ->orderBy('issued_date', 'desc')
            ->paginate(12);
        
        return view('volunteer.certificates.index', compact('certificates'));
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $user = Auth::user();
        
        // Check if user owns this certificate or if it's public
        if ($certificate->user_id !== $user->id && !$certificate->is_public) {
            abort(403, 'Unauthorized to view this certificate.');
        }
        
        $certificate->load(['user', 'event', 'organization']);
        
        return view('volunteer.certificates.show', compact('certificate'));
    }

    /**
     * Download certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        $user = Auth::user();
        
        // Check if user owns this certificate
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Unauthorized to download this certificate.');
        }
        
        // If PDF doesn't exist, generate it
        if (!$certificate->file_path || !Storage::exists($certificate->file_path)) {
            $this->generateCertificatePDF($certificate);
        }
        
        $filename = $certificate->certificate_number . '.pdf';
        
        return Storage::download($certificate->file_path, $filename);
    }



    /**
     * Generate PDF for certificate.
     */
    private function generateCertificatePDF(Certificate $certificate)
    {
        // Load certificate with relationships
        $certificate->load(['user', 'event', 'organization']);
        
        // Generate PDF
        $pdf = Pdf::loadView('certificates.template', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'serif',
                'isRemoteEnabled' => true,
            ]);

        // Create filename
        $filename = 'certificates/' . $certificate->certificate_number . '.pdf';
        
        // Save PDF to storage
        Storage::put($filename, $pdf->output());
        
        // Update certificate with file path
        $certificate->update(['file_path' => $filename]);
        
        return $filename;
    }

    /**
     * Share or unshare a certificate (make it public/private)
     */
    public function share(Certificate $certificate)
    {
        $this->authorize('view', $certificate);
        
        $certificate->update([
            'is_public' => !$certificate->is_public
        ]);
        
        return response()->json([
            'success' => true,
            'is_public' => $certificate->is_public,
            'message' => $certificate->is_public ? 'Certificate is now public' : 'Certificate is now private'
        ]);
    }

    /**
     * Show certificate verification form
     */
    public function showVerify()
    {
        return view('volunteer.certificates.verify');
    }

    /**
     * Verify a certificate by verification code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:12'
        ]);

        $certificate = Certificate::where('verification_code', $request->verification_code)
            ->where('is_verified', true)
            ->with(['user', 'event', 'organization'])
            ->first();

        if (!$certificate) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid verification code or certificate not found.'
            ]);
        }

        return response()->json([
            'valid' => true,
            'certificate' => [
                'certificate_number' => $certificate->certificate_number,
                'recipient_name' => $certificate->user->name,
                'event_title' => $certificate->event->title,
                'organization_name' => $certificate->organization->name,
                'hours_completed' => $certificate->hours_completed,
                'issued_date' => $certificate->issued_date->format('F j, Y'),
                'type' => $certificate->type,
                'description' => $certificate->description,
                'verified_at' => $certificate->verified_at->format('F j, Y')
            ]
        ]);
    }
}
