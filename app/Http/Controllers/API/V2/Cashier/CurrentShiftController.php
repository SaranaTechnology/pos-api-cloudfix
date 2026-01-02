<?php

namespace App\Http\Controllers\API\V2\Cashier;

use Domain\Cashier\Actions\GetCurrentShiftAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class CurrentShiftController extends BaseController
{
    public function __invoke(Request $request)
    {
        $staff = $request->user('staff');
        $shift = GetCurrentShiftAction::resolve()->execute($staff);

        return $this->resolveForSuccessResponseWith(
            message: $shift ? 'Shift aktif ditemukan' : 'Tidak ada shift aktif',
            data: $shift
        );
    }
}
