<?php

namespace App\Http\Controllers\API\V1\Setting;

use Illuminate\Support\Facades\Log;
use Infra\Setting\Models\Setting;
use Infra\Shared\Controllers\BaseController;

class OpenSettingsController extends BaseController
{
    public function __invoke()
    {
        try {
            $data = Setting::first();

            return $this->resolveForSuccessResponseWith(
                message: 'list Settings',
                data: $data
            );

        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return $this->resolveForFailedResponseWith(
                message: $th->getMessage()
            );
        }
    }
}
