<?php

namespace App\Services;

use App\Models\ApiIntegration;
use App\Models\ApiCallLog;
use Illuminate\Support\Facades\Http;

class ApiIntegrationService
{
    /**
     * Call external API
     */
    public function callApi($integrationName, $endpoint, $method = 'GET', $data = [], $headers = [])
    {
        $startTime = microtime(true);

        try {
            $integration = ApiIntegration::where('name', $integrationName)
                ->where('status', 'active')
                ->firstOrFail();

            if (!$integration->isActive()) {
                throw new \Exception('Integration is not active');
            }

            $url = $integration->api_url . $endpoint;
            $defaultHeaders = [
                'Authorization' => 'Bearer ' . $integration->api_key,
                'Content-Type' => 'application/json',
            ];

            $allHeaders = array_merge($defaultHeaders, $headers);

            $response = $this->makeHttpRequest($method, $url, $data, $allHeaders);

            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $this->logApiCall(
                $integrationName,
                $endpoint,
                $method,
                $data,
                $response->json(),
                $response->status(),
                'success',
                null,
                $responseTime
            );

            return [
                'success' => true,
                'data' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000;

            $this->logApiCall(
                $integrationName,
                $endpoint,
                $method,
                $data,
                null,
                null,
                'error',
                $e->getMessage(),
                $responseTime
            );

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Make HTTP request
     */
    private function makeHttpRequest($method, $url, $data, $headers)
    {
        if ($method === 'GET') {
            return Http::withHeaders($headers)->get($url, $data);
        } elseif ($method === 'POST') {
            return Http::withHeaders($headers)->post($url, $data);
        } elseif ($method === 'PUT') {
            return Http::withHeaders($headers)->put($url, $data);
        } elseif ($method === 'DELETE') {
            return Http::withHeaders($headers)->delete($url, $data);
        }

        throw new \Exception('Unsupported HTTP method');
    }

    /**
     * Test integration
     */
    public function testIntegration($integrationName)
    {
        $integration = ApiIntegration::where('name', $integrationName)->firstOrFail();

        try {
            $response = Http::timeout(10)->get($integration->api_url, [
                'key' => $integration->api_key,
            ]);

            $integration->update([
                'status' => 'active',
                'last_tested_at' => now(),
                'last_error' => null,
            ]);

            return ['success' => true, 'message' => 'Integration test successful'];
        } catch (\Exception $e) {
            $integration->update([
                'status' => 'error',
                'last_tested_at' => now(),
                'last_error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Log API call
     */
    private function logApiCall($integration, $endpoint, $method, $request, $response, $code, $status, $error, $responseTime)
    {
        ApiCallLog::create([
            'integration_name' => $integration,
            'endpoint' => $endpoint,
            'method' => $method,
            'request_payload' => json_encode($request),
            'response_code' => $code,
            'response_payload' => $response ? json_encode($response) : null,
            'status' => $status,
            'response_time_ms' => $responseTime,
            'error_message' => $error,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Get integration
     */
    public function getIntegration($name)
    {
        return ApiIntegration::where('name', $name)->first();
    }

    /**
     * Get active integrations
     */
    public function getActiveIntegrations()
    {
        return ApiIntegration::where('status', 'active')->get();
    }

    /**
     * Get API call logs
     */
    public function getApiLogs($integrationName, $limit = 100)
    {
        return ApiCallLog::where('integration_name', $integrationName)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
