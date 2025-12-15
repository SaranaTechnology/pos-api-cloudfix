<?php

namespace App\Http\Controllers\API\V1\Health;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Basic health check endpoint for load balancers and container orchestration.
     */
    public function check(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'service' => 'api-cashier-pos',
            'client' => config('app.client_name', env('CLIENT_NAME', 'default')),
        ]);
    }

    /**
     * Liveness probe - indicates if the application is running.
     */
    public function liveness(): JsonResponse
    {
        return response()->json([
            'status' => 'alive',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Readiness probe - indicates if the application is ready to receive traffic.
     */
    public function readiness(): JsonResponse
    {
        $checks = [];
        $isHealthy = true;

        // Check Database
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'healthy',
                'driver' => config('database.default'),
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
            $isHealthy = false;
        }

        // Check Cache
        try {
            Cache::put('health_check', true, 10);
            Cache::forget('health_check');
            $checks['cache'] = [
                'status' => 'healthy',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            $checks['cache'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
            $isHealthy = false;
        }

        $statusCode = $isHealthy ? 200 : 503;

        return response()->json([
            'status' => $isHealthy ? 'ready' : 'not_ready',
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $statusCode);
    }

    /**
     * Detailed health check with system information.
     */
    public function detailed(): JsonResponse
    {
        $checks = [];
        $isHealthy = true;

        // Database Check
        try {
            $dbStart = microtime(true);
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);

            $checks['database'] = [
                'status' => 'healthy',
                'driver' => config('database.default'),
                'latency_ms' => $dbLatency,
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
            $isHealthy = false;
        }

        // System Information
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => config('app.env'),
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
        ];

        $statusCode = $isHealthy ? 200 : 503;

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'service' => 'api-cashier-pos',
            'client' => config('app.client_name', env('CLIENT_NAME', 'default')),
            'checks' => $checks,
            'system' => $systemInfo,
        ], $statusCode);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
