<?php

namespace App\Services;

use App\Models\WorkPermitVerification;
use App\Models\GovernmentApiLog;
use Illuminate\Support\Facades\Http;

class MohreService
{
    protected $apiBaseUrl;
    protected $apiKey;
    protected $timeout = 30;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.mohre.base_url');
        $this->apiKey = config('services.mohre.api_key');
    }

    /**
     * Verify work permit
     */
    public function verifyWorkPermit($workPermitNumber, $userId)
    {
        try {
            $startTime = microtime(true);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiBaseUrl . '/verify-work-permit', [
                    'work_permit_number' => $workPermitNumber,
                ])
                ->json();

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $this->logApiCall(
                'mohre',
                '/verify-work-permit',
                'POST',
                ['work_permit_number' => $workPermitNumber],
                $response,
                200,
                'success',
                null,
                $responseTime
            );

            if ($response['status'] === 'found' || $response['status'] === 'valid') {
                return $this->storeVerification($userId, $workPermitNumber, $response['data'], 'valid');
            } else {
                return $this->storeVerification($userId, $workPermitNumber, $response, 'not_found');
            }
        } catch (\Exception $e) {
            $this->logApiCall(
                'mohre',
                '/verify-work-permit',
                'POST',
                ['work_permit_number' => $workPermitNumber],
                null,
                null,
                'error',
                $e->getMessage(),
                null
            );

            return $this->storeVerification($userId, $workPermitNumber, null, 'not_found', $e->getMessage());
        }
    }

    /**
     * Check work permit status
     */
    public function checkStatus($workPermitNumber, $userId)
    {
        try {
            $startTime = microtime(true);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->get($this->apiBaseUrl . '/work-permit-status/' . $workPermitNumber)
                ->json();

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $this->logApiCall(
                'mohre',
                '/work-permit-status',
                'GET',
                ['work_permit_number' => $workPermitNumber],
                $response,
                200,
                'success',
                null,
                $responseTime
            );

            $status = $this->mapStatusResponse($response['status'] ?? null);

            return $this->storeVerification($userId, $workPermitNumber, $response, $status);
        } catch (\Exception $e) {
            $this->logApiCall(
                'mohre',
                '/work-permit-status',
                'GET',
                ['work_permit_number' => $workPermitNumber],
                null,
                null,
                'error',
                $e->getMessage(),
                null
            );

            return $this->storeVerification($userId, $workPermitNumber, null, 'not_found', $e->getMessage());
        }
    }

    /**
     * Store work permit verification
     */
    private function storeVerification($userId, $workPermitNumber, $data, $status, $error = null)
    {
        return WorkPermitVerification::updateOrCreate(
            ['user_id' => $userId, 'work_permit_number' => $workPermitNumber],
            [
                'sponsor_name' => $data['sponsor_name'] ?? null,
                'sponsor_trade_license' => $data['sponsor_trade_license'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'status' => $status,
                'issue_date' => $data['issue_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'permit_data' => $data,
                'error_message' => $error,
                'verified_at' => in_array($status, ['valid', 'verified']) ? now() : null,
            ]
        );
    }

    /**
     * Get work permit verification
     */
    public function getVerification($userId)
    {
        return WorkPermitVerification::where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Check if user has valid work permit
     */
    public function hasValidPermit($userId): bool
    {
        $permit = $this->getVerification($userId);
        return $permit && $permit->isValid();
    }

    /**
     * Map API status response
     */
    private function mapStatusResponse($apiStatus)
    {
        $statusMap = [
            'valid' => 'valid',
            'active' => 'valid',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            'not_found' => 'not_found',
        ];

        return $statusMap[$apiStatus] ?? 'not_found';
    }

    /**
     * Log API call
     */
    protected function logApiCall($service, $endpoint, $method, $request, $response, $code, $status, $error, $responseTime)
    {
        GovernmentApiLog::create([
            'service' => $service,
            'endpoint' => $endpoint,
            'method' => $method,
            'request_payload' => json_encode($request),
            'response_payload' => $response ? json_encode($response) : null,
            'response_code' => $code,
            'status' => $status,
            'error_message' => $error,
            'response_time_ms' => $responseTime,
            'ip_address' => request()->ip(),
        ]);
    }
}
