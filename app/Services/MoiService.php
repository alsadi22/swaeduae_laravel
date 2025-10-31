<?php

namespace App\Services;

use App\Models\MoiVerification;
use App\Models\WorkPermitVerification;
use App\Models\VisaStatus;
use App\Models\GovernmentApiLog;
use Illuminate\Support\Facades\Http;

class MoiService
{
    protected $apiBaseUrl;
    protected $apiKey;
    protected $timeout = 30;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.moi.base_url');
        $this->apiKey = config('services.moi.api_key');
    }

    /**
     * Verify identity with MOI
     */
    public function verify($name, $passportNumber, $userId)
    {
        try {
            $startTime = microtime(true);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiBaseUrl . '/verify-identity', [
                    'name' => $name,
                    'passport_number' => $passportNumber,
                ])
                ->json();

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $this->logApiCall(
                'moi',
                '/verify-identity',
                'POST',
                compact('name', 'passportNumber'),
                $response,
                200,
                'success',
                null,
                $responseTime
            );

            if ($response['status'] === 'found') {
                return $this->storeVerification($userId, $response, 'verified');
            } else {
                return $this->storeVerification($userId, $response, 'not_found');
            }
        } catch (\Exception $e) {
            $this->logApiCall(
                'moi',
                '/verify-identity',
                'POST',
                compact('name', 'passportNumber'),
                null,
                null,
                'error',
                $e->getMessage(),
                null
            );

            return $this->storeVerification($userId, null, 'not_found', $e->getMessage());
        }
    }

    /**
     * Check visa status
     */
    public function checkVisaStatus($passportNumber, $userId)
    {
        try {
            $startTime = microtime(true);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get($this->apiBaseUrl . '/visa-status', [
                    'passport' => $passportNumber,
                ])
                ->json();

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $this->logApiCall(
                'moi',
                '/visa-status',
                'GET',
                ['passport' => $passportNumber],
                $response,
                200,
                'success',
                null,
                $responseTime
            );

            return $this->storeVisaStatus($userId, $response, $passportNumber);
        } catch (\Exception $e) {
            $this->logApiCall(
                'moi',
                '/visa-status',
                'GET',
                ['passport' => $passportNumber],
                null,
                null,
                'error',
                $e->getMessage(),
                null
            );

            return $this->storeVisaStatus($userId, null, $passportNumber, 'unknown', $e->getMessage());
        }
    }

    /**
     * Store MOI verification
     */
    private function storeVerification($userId, $data, $status, $error = null)
    {
        $referenceNumber = 'MOI-' . time() . '-' . $userId;

        return MoiVerification::updateOrCreate(
            ['user_id' => $userId],
            [
                'reference_number' => $referenceNumber,
                'name' => $data['name'] ?? null,
                'passport_number' => $data['passport_number'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'status' => $status,
                'response_data' => $data,
                'error_message' => $error,
                'verified_at' => $status === 'verified' ? now() : null,
            ]
        );
    }

    /**
     * Store visa status
     */
    private function storeVisaStatus($userId, $data, $passportNumber, $status = 'valid', $error = null)
    {
        if (!$data || !isset($data['visa_type'])) {
            $status = 'unknown';
        }

        return VisaStatus::updateOrCreate(
            ['user_id' => $userId],
            [
                'visa_type' => $data['visa_type'] ?? 'unknown',
                'visa_number' => $data['visa_number'] ?? null,
                'passport_number' => $passportNumber,
                'issue_date' => $data['issue_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'status' => $status,
                'entry_point' => $data['entry_point'] ?? null,
                'last_entry_date' => $data['last_entry_date'] ?? null,
                'additional_data' => $data,
                'last_verified_at' => now(),
            ]
        );
    }

    /**
     * Get MOI verification
     */
    public function getVerification($userId)
    {
        return MoiVerification::where('user_id', $userId)->latest()->first();
    }

    /**
     * Get visa status
     */
    public function getVisaStatus($userId)
    {
        return VisaStatus::where('user_id', $userId)->latest()->first();
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
