<?php

namespace App\Http\Controllers\Api;

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
     * Get user compliance status
     */
    public function complianceStatus()
    {
        $status = $this->complianceService->getUserComplianceStatus(Auth::id());

        return response()->json($status);
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

            return response()->json([
                'status' => $verification->status,
                'data' => $verification,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get Emirates ID verification
     */
    public function getEmiratesIdVerification()
    {
        $verification = $this->emiratesIdService->getVerification(Auth::id());

        return response()->json($verification);
    }

    /**
     * Verify with MOI
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

            return response()->json([
                'status' => $verification->status,
                'data' => $verification,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get MOI verification
     */
    public function getMoiVerification()
    {
        $verification = $this->moiService->getVerification(Auth::id());

        return response()->json($verification);
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

            return response()->json([
                'status' => $visa->status,
                'data' => $visa,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get visa status
     */
    public function getVisaStatus()
    {
        $visa = $this->moiService->getVisaStatus(Auth::id());

        return response()->json($visa);
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

            return response()->json([
                'status' => $permit->status,
                'data' => $permit,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get work permit verification
     */
    public function getWorkPermit()
    {
        $permit = $this->mohreService->getVerification(Auth::id());

        return response()->json($permit);
    }

    /**
     * Perform full compliance check
     */
    public function performComplianceCheck()
    {
        try {
            $result = $this->complianceService->performFullComplianceCheck(Auth::id());

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get compliance history
     */
    public function complianceHistory()
    {
        $records = \App\Models\ComplianceRecord::where('user_id', Auth::id())
            ->orderBy('checked_at', 'desc')
            ->paginate(20);

        return response()->json($records);
    }

    /**
     * Get API logs
     */
    public function apiLogs()
    {
        $logs = \App\Models\GovernmentApiLog::recent(1440)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'logs' => $logs,
            'success_count' => $logs->where('status', 'success')->count(),
            'failed_count' => $logs->where('status', '!=', 'success')->count(),
        ]);
    }
}
