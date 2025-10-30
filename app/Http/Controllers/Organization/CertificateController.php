<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\Application;
use App\Models\Attendance;
use App\Events\CertificateGenerated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Display a listing of certificates for the organization.
     */
    public function index()
    {
        $user = Auth::user();
        $organizationIds = $user->organizations->pluck('id');
        
        $certificates = Certificate::with(['user', 'event', 'organization'])
            ->whereIn('organization_id', $organizationIds)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('organization.certificates.index', compact('certificates'));
    }

    /**
     * Generate certificates for completed events.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'certificate_type' => 'required|in:volunteer,completion,achievement',
            'hours_completed' => 'required|numeric|min:0.5|max:999.99',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $event = Event::findOrFail($request->event_id);
        
        // Check if user has permission to generate certificates for this event
        if (!$user->organizations->contains($event->organization_id)) {
            abort(403, 'Unauthorized to generate certificates for this event.');
        }

        $generatedCertificates = [];
        $errors = [];

        foreach ($request->user_ids as $userId) {
            try {
                // Check if user has completed attendance for this event
                $attendance = Attendance::where('event_id', $event->id)
                    ->where('user_id', $userId)
                    ->where('status', 'completed')
                    ->first();

                if (!$attendance) {
                    $errors[] = "User ID {$userId} has not completed attendance for this event.";
                    continue;
                }

                // Check if certificate already exists
                $existingCertificate = Certificate::where('event_id', $event->id)
                    ->where('user_id', $userId)
                    ->first();

                if ($existingCertificate) {
                    $errors[] = "Certificate already exists for User ID {$userId} for this event.";
                    continue;
                }

                // Create certificate
                $certificate = Certificate::create([
                    'certificate_number' => Certificate::generateCertificateNumber(),
                    'user_id' => $userId,
                    'event_id' => $event->id,
                    'organization_id' => $event->organization_id,
                    'type' => $request->certificate_type,
                    'title' => "Certificate of {$request->certificate_type} Service",
                    'description' => $request->description ?? "This certificate is awarded for outstanding volunteer service in {$event->title}.",
                    'hours_completed' => $request->hours_completed,
                    'event_date' => $event->start_date,
                    'issued_date' => Carbon::now(),
                    'verification_code' => Certificate::generateVerificationCode(),
                    'is_verified' => true,
                    'verified_at' => Carbon::now(),
                    'verified_by' => $user->id,
                    'is_public' => true,
                ]);

                // Generate PDF
                $this->generateCertificatePDF($certificate);
                
                // Broadcast certificate generated event
                broadcast(new CertificateGenerated($certificate));
                
                $generatedCertificates[] = $certificate;

            } catch (\Exception $e) {
                $errors[] = "Error generating certificate for User ID {$userId}: " . $e->getMessage();
            }
        }

        $message = count($generatedCertificates) . ' certificate(s) generated successfully.';
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' ', $errors);
        }

        return redirect()->back()->with('success', $message);
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
     * Show certificate generation form.
     */
    public function create(Event $event)
    {
        $user = Auth::user();
        
        // Check if user has permission to generate certificates for this event
        if (!$user->organizations->contains($event->organization_id)) {
            abort(403, 'Unauthorized to generate certificates for this event.');
        }

        // Get users who have completed attendance for this event
        $completedAttendances = Attendance::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'completed')
            ->get();

        // Filter out users who already have certificates
        $eligibleUsers = $completedAttendances->filter(function ($attendance) use ($event) {
            return !Certificate::where('event_id', $event->id)
                ->where('user_id', $attendance->user_id)
                ->exists();
        });

        return view('organization.certificates.create', compact('event', 'eligibleUsers'));
    }
}
