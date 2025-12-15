<?php

namespace App\Http\Controllers\API\V1\Health\Checkers;

class StorageHealthChecker implements HealthCheckerInterface
{
    public function check(): array
    {
        try {
            $testFile = storage_path('app/health_check_' . time());
            file_put_contents($testFile, 'test');
            unlink($testFile);

            return [
                'status' => 'healthy',
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
        return 'storage';
    }
}
