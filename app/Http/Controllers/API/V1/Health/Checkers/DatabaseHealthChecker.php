<?php

namespace App\Http\Controllers\API\V1\Health\Checkers;

use Illuminate\Support\Facades\DB;

class DatabaseHealthChecker implements HealthCheckerInterface
{
    public function check(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'healthy',
                'driver' => config('database.default'),
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
        return 'database';
    }
}
