<?php

namespace App\Http\Controllers\API\V1\Health\Checkers;

use Illuminate\Support\Facades\Cache;

class CacheHealthChecker implements HealthCheckerInterface
{
    public function check(): array
    {
        try {
            $start = microtime(true);
            $key = 'health_check_' . time();

            Cache::put($key, true, 10);
            Cache::get($key);
            Cache::forget($key);

            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'driver' => config('cache.default'),
                'latency_ms' => $latency,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getName(): string
    {
        return 'cache';
    }
}
