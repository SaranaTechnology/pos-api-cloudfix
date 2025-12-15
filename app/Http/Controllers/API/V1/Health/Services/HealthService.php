<?php

namespace App\Http\Controllers\API\V1\Health\Services;

use App\Http\Controllers\API\V1\Health\Checkers\HealthCheckerInterface;
use App\Http\Controllers\API\V1\Health\Checkers\SystemInfoCollector;

class HealthService
{
    private string $serviceName = 'api-cashier-pos';

    /** @var HealthCheckerInterface[] */
    private array $checkers;

    private SystemInfoCollector $systemInfoCollector;

    public function __construct(array $checkers, SystemInfoCollector $systemInfoCollector)
    {
        $this->checkers = $checkers;
        $this->systemInfoCollector = $systemInfoCollector;
    }

    public function basicCheck(): array
    {
        return [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'service' => $this->serviceName,
            'client' => config('app.client_name', env('CLIENT_NAME', 'default')),
        ];
    }

    public function livenessCheck(): array
    {
        return [
            'status' => 'alive',
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function readinessCheck(): array
    {
        $checks = $this->runCheckers();
        $isHealthy = $this->isAllHealthy($checks);

        return [
            'status' => $isHealthy ? 'ready' : 'not_ready',
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
            'is_healthy' => $isHealthy,
        ];
    }

    public function detailedCheck(): array
    {
        $checks = $this->runCheckers();
        $isHealthy = $this->isAllHealthy($checks);

        return [
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'service' => $this->serviceName,
            'client' => config('app.client_name', env('CLIENT_NAME', 'default')),
            'checks' => $checks,
            'system' => $this->systemInfoCollector->collect(),
            'is_healthy' => $isHealthy,
        ];
    }

    private function runCheckers(): array
    {
        $results = [];
        foreach ($this->checkers as $checker) {
            $results[$checker->getName()] = $checker->check();
        }
        return $results;
    }

    private function isAllHealthy(array $checks): bool
    {
        foreach ($checks as $check) {
            if ($check['status'] !== 'healthy') {
                return false;
            }
        }
        return true;
    }
}
