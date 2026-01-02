<?php

namespace App\Http\Controllers\API\V2\Staff\Auth;

use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class GetStaffAuthController extends BaseController
{
    public function __invoke(Request $request)
    {
        $staff = $request->user('staff');

        return $this->resolveForSuccessResponseWith(
            message: 'Data staff berhasil diambil',
            data: $staff
        );
    }
}
