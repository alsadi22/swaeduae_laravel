<?php

namespace App\Services;

use App\Models\EmiratesIdVerification;
use App\Models\GovernmentApiLog;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class EmiratesIdService
{
    protected $apiBaseUrl;
    protected $apiKey;
    protected $timeout = 30;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.emirates_id.base_url');
        $this->apiKey = config('services.emirates_id.api_key');
    }

    /**
     * Verify Emirates ID
     */
    public function verify($emiratesId, $userId)
    {
        try {
            $startTime = microtime(true);

            // Call Emirates ID verification API
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiBaseUrl . '/verify', [
                    'emirates_id' => $emiratesId,
                ])
                ->json();

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            // Log API call
            $this->logApiCall(
                'emirates_id',
                '/verify',
                'POST',
                ['emirates_id' => $emiratesId],
                $response,
                200,
                'success',
                null,
                $responseTime
            );

            // Process response
            if ($response['status'] === 'success') {
                return $this->storeVerification($userId, $emiratesId, $response['data'], 'verified');
            } else {
                return $this->storeVerification($userId, $emiratesId, $response, 'failed', $response['message'] ?? 'Verification failed');
            }
        } catch (\Exception $e) {
            $this->logApiCall(
                'emirates_id',
                '/verify',
                'POST',
                ['emirates_id' => $emiratesId],
                null,
                null,
                'error',
                $e->getMessage(),
                null
            );

            return $this->storeVerification($userId, $emiratesId, null, 'failed', $e->getMessage());
        }
    }

    /**
     * Store verification result
     */
    private function storeVerification($userId, $emiratesId, $data, $status, $error = null)
    {
        $verification = EmiratesIdVerification::updateOrCreate(
            ['user_id' => $userId, 'emirates_id' => $emiratesId],
            [
                'first_name_en' => $data['first_name_en'] ?? null,
                'last_name_en' => $data['last_name_en'] ?? null,
                'first_name_ar' => $data['first_name_ar'] ?? null,
                'last_name_ar' => $data['last_name_ar'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => $status,
                'verification_data' => $data,
                'verification_error' => $error,
                'verified_at' => $status === 'verified' ? now() : null,
                'expires_at' => $status === 'verified' ? now()->addYears(1) : null,
            ]
        );

        return $verification;
    }

    /**
     * Get verification for user
     */
    public function getVerification($userId)
    {
        return EmiratesIdVerification::where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Check if user is verified
     */
    public function isVerified($userId): bool
    {
        $verification = $this->getVerification($userId);
        return $verification && $verification->status === 'verified' && !$verification->isExpired();
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
