<?php

namespace App\Services;

use App\Models\User;
use App\Models\ComplianceRecord;
use App\Models\CriminalRecordCheck;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\Http;

class GovernmentComplianceService
{
    protected $emiratesIdService;
    protected $moiService;
    protected $mohreService;

    public function __construct(
        EmiratesIdService $emiratesIdService,
        MoiService $moiService,
        MohreService $mohreService
    ) {
        $this->emiratesIdService = $emiratesIdService;
        $this->moiService = $moiService;
        $this->mohreService = $mohreService;
    }

    /**
     * Perform complete compliance check
     */
    public function performFullComplianceCheck($userId)
    {
        $user = User::findOrFail($userId);
        $results = [];

        // Check Emirates ID
        if ($user->emirates_id) {
            $results['emirates_id'] = $this->checkEmiratesId($user);
        }

        // Check MOI
        if ($user->nationality) {
            $results['moi'] = $this->checkMoi($user);
        }

        // Check Work Permit
        if ($user->unique_id) {
            $results['work_permit'] = $this->checkWorkPermit($user);
        }

        // Check Criminal Record
        $results['criminal_record'] = $this->checkCriminalRecord($user);

        // Check Sponsorship
        $results['sponsorship'] = $this->checkSponsorship($user);

        // Create overall compliance record
        $overallStatus = $this->determineOverallStatus($results);
        $this->storeComplianceRecord($userId, $results, $overallStatus);

        return [
            'user_id' => $userId,
            'overall_status' => $overallStatus,
            'checks' => $results,
            'checked_at' => now(),
        ];
    }

    /**
     * Check Emirates ID compliance
     */
    private function checkEmiratesId($user)
    {
        try {
            $verification = $this->emiratesIdService->verify($user->emirates_id, $user->id);

            return [
                'status' => $verification->status === 'verified' ? 'compliant' : 'non_compliant',
                'data' => $verification,
                'message' => $verification->status === 'verified' ? 'Emirates ID verified' : 'Emirates ID verification failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check MOI compliance
     */
    private function checkMoi($user)
    {
        try {
            $verification = $this->moiService->verify(
                $user->name,
                $user->emirate,
                $user->id
            );

            $visa = $this->moiService->checkVisaStatus($user->emirate, $user->id);

            return [
                'status' => $verification->status === 'verified' ? 'compliant' : 'non_compliant',
                'verification' => $verification,
                'visa_status' => $visa->status,
                'message' => 'MOI check completed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check Work Permit compliance
     */
    private function checkWorkPermit($user)
    {
        if (!$user->unique_id) {
            return [
                'status' => 'pending',
                'message' => 'No work permit number available',
            ];
        }

        try {
            $permit = $this->mohreService->verifyWorkPermit($user->unique_id, $user->id);

            return [
                'status' => $permit->status === 'valid' ? 'compliant' : 'non_compliant',
                'data' => $permit,
                'message' => "Work permit status: {$permit->status}",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check criminal record compliance
     */
    private function checkCriminalRecord($user)
    {
        try {
            $check = CriminalRecordCheck::where('user_id', $user->id)
                ->where('status', '!=', 'pending')
                ->latest()
                ->first();

            if (!$check) {
                return [
                    'status' => 'pending',
                    'message' => 'No criminal record check available',
                ];
            }

            return [
                'status' => $check->status === 'clear' ? 'compliant' : 'non_compliant',
                'data' => $check,
                'message' => $check->result_message,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check sponsorship compliance
     */
    private function checkSponsorship($user)
    {
        try {
            $sponsorship = Sponsorship::where('user_id', $user->id)
                ->where('status', 'active')
                ->latest()
                ->first();

            if (!$sponsorship) {
                return [
                    'status' => 'pending',
                    'message' => 'No active sponsorship found',
                ];
            }

            return [
                'status' => $sponsorship->isActive() ? 'compliant' : 'non_compliant',
                'data' => $sponsorship,
                'message' => "Sponsorship type: {$sponsorship->sponsor_type}",
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Determine overall compliance status
     */
    private function determineOverallStatus($results)
    {
        $statuses = array_column($results, 'status');

        if (in_array('non_compliant', $statuses)) {
            return 'non_compliant';
        }

        if (in_array('error', $statuses)) {
            return 'error';
        }

        if (in_array('pending', $statuses)) {
            return 'pending';
        }

        return 'compliant';
    }

    /**
     * Store compliance record
     */
    private function storeComplianceRecord($userId, $results, $overallStatus)
    {
        foreach ($results as $checkType => $result) {
            ComplianceRecord::create([
                'user_id' => $userId,
                'check_type' => $checkType,
                'status' => $result['status'] ?? 'error',
                'details' => json_encode($result),
                'authority' => $this->getAuthority($checkType),
                'checked_at' => now(),
                'expires_at' => now()->addDays(90),
            ]);
        }
    }

    /**
     * Get authority for check type
     */
    private function getAuthority($checkType)
    {
        $authorities = [
            'emirates_id' => 'General Directorate of Residency and Foreigners Affairs',
            'moi' => 'Ministry of Interior',
            'work_permit' => 'Ministry of Human Resources and Emiratisation',
            'criminal_record' => 'Ministry of Interior',
            'sponsorship' => 'Ministry of Human Resources and Emiratisation',
        ];

        return $authorities[$checkType] ?? 'Unknown Authority';
    }

    /**
     * Get user compliance status
     */
    public function getUserComplianceStatus($userId)
    {
        $records = ComplianceRecord::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->latest()
            ->get()
            ->groupBy('check_type');

        return [
            'records' => $records,
            'overall_status' => $this->calculateOverallStatus($records),
            'last_checked' => ComplianceRecord::where('user_id', $userId)->latest()->first()?->checked_at,
        ];
    }

    /**
     * Calculate overall compliance status
     */
    private function calculateOverallStatus($records)
    {
        if ($records->isEmpty()) {
            return 'unknown';
        }

        $nonCompliant = $records->filter(fn ($group) => $group->first()->status === 'non_compliant');

        if ($nonCompliant->isNotEmpty()) {
            return 'non_compliant';
        }

        return 'compliant';
    }
}
