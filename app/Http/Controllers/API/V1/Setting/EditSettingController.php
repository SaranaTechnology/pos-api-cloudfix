<?php

namespace App\Http\Controllers\API\V1\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Infra\Setting\Models\Setting;
use Infra\Shared\Controllers\BaseController;

class EditSettingController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {
            $setting = Setting::first();
            $setting->update($req->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Settings berhasil di update',
                data: $setting
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return $this->resolveForFailedResponseWith(
                message: $th->getMessage()
            );
        }
    }
}
