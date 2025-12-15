<?php

namespace App\Http\Controllers\API\V1\Health;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\V1\Health\Checkers\CacheHealthChecker;
use App\Http\Controllers\API\V1\Health\Checkers\DatabaseHealthChecker;
use App\Http\Controllers\API\V1\Health\Checkers\StorageHealthChecker;
use App\Http\Controllers\API\V1\Health\Checkers\SystemInfoCollector;
use App\Http\Controllers\API\V1\Health\Services\HealthService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    private HealthService $healthService;

    public function __construct()
    {
        $checkers = [
            new DatabaseHealthChecker(),
            new CacheHealthChecker(),
            new StorageHealthChecker(),
        ];

        $this->healthService = new HealthService(
            $checkers,
            new SystemInfoCollector()
        );
    }

    public function check(): JsonResponse
    {
        return response()->json($this->healthService->basicCheck());
    }

    public function liveness(): JsonResponse
    {
        return response()->json($this->healthService->livenessCheck());
    }

    public function readiness(): JsonResponse
    {
        $result = $this->healthService->readinessCheck();
        $statusCode = $result['is_healthy'] ? 200 : 503;
        unset($result['is_healthy']);

        return response()->json($result, $statusCode);
    }

    public function detailed(): JsonResponse
    {
        $result = $this->healthService->detailedCheck();
        $statusCode = $result['is_healthy'] ? 200 : 503;
        unset($result['is_healthy']);

        return response()->json($result, $statusCode);
    }
}
