<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmiratesIdService;
use App\Services\MoiService;
use App\Services\MohreService;
use App\Services\GovernmentComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GovernmentVerificationController extends Controller
{
    protected $emiratesIdService;
    protected $moiService;
    protected $mohreService;
    protected $complianceService;

    public function __construct(
        EmiratesIdService $emiratesIdService,
        MoiService $moiService,
        MohreService $mohreService,
        GovernmentComplianceService $complianceService
    ) {
        $this->emiratesIdService = $emiratesIdService;
        $this->moiService = $moiService;
        $this->mohreService = $mohreService;
        $this->complianceService = $complianceService;
    }

    /**
     * Show verification page
     */
    public function index()
    {
        $user = Auth::user();
        $complianceStatus = $this->complianceService->getUserComplianceStatus($user->id);
        $emiratesIdVerification = $this->emiratesIdService->getVerification($user->id);
        $moiVerification = $this->moiService->getVerification($user->id);
        $visaStatus = $this->moiService->getVisaStatus($user->id);
        $workPermit = $this->mohreService->getVerification($user->id);

        return view('auth.government-verification', compact(
            'complianceStatus',
            'emiratesIdVerification',
            'moiVerification',
            'visaStatus',
            'workPermit'
        ));
    }

    /**
     * Verify Emirates ID
     */
    public function verifyEmiratesId(Request $request)
    {
        $validated = $request->validate([
            'emirates_id' => 'required|string|size:15',
        ]);

        try {
            $verification = $this->emiratesIdService->verify(
                $validated['emirates_id'],
                Auth::id()
            );

            if ($verification->status === 'verified') {
                return back()->with('success', 'Emirates ID verified successfully!');
            } else {
                return back()->with('error', $verification->verification_error ?? 'Verification failed');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Verify MOI
     */
    public function verifyMoi(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'passport_number' => 'nullable|string',
        ]);

        try {
            $verification = $this->moiService->verify(
                $validated['name'],
                $validated['passport_number'] ?? '',
                Auth::id()
            );

            if ($verification->status === 'verified') {
                return back()->with('success', 'MOI verification completed!');
            } else {
                return back()->with('warning', 'Could not verify with MOI');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Check visa status
     */
    public function checkVisaStatus(Request $request)
    {
        $validated = $request->validate([
            'passport_number' => 'required|string',
        ]);

        try {
            $visa = $this->moiService->checkVisaStatus(
                $validated['passport_number'],
                Auth::id()
            );

            if ($visa->status === 'valid') {
                return back()->with('success', 'Visa is valid until: ' . $visa->expiry_date);
            } else {
                return back()->with('warning', 'Visa status: ' . $visa->status);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Verify work permit
     */
    public function verifyWorkPermit(Request $request)
    {
        $validated = $request->validate([
            'work_permit_number' => 'required|string',
        ]);

        try {
            $permit = $this->mohreService->verifyWorkPermit(
                $validated['work_permit_number'],
                Auth::id()
            );

            if ($permit->status === 'valid') {
                return back()->with('success', 'Work permit verified successfully!');
            } else {
                return back()->with('warning', 'Work permit status: ' . $permit->status);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Perform full compliance check
     */
    public function performComplianceCheck()
    {
        try {
            $result = $this->complianceService->performFullComplianceCheck(Auth::id());

            if ($result['overall_status'] === 'compliant') {
                return back()->with('success', 'Compliance check passed!');
            } else {
                return back()->with('warning', 'Compliance check status: ' . $result['overall_status']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
