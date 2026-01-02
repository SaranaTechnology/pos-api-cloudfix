<?php

namespace App\Http\Controllers\API\V2\Cashier;

use Domain\Cashier\Actions\GetDailySummaryAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class DailySummaryController extends BaseController
{
    public function __invoke(Request $request)
    {
        $staff = $request->user('staff');
        $date = $request->query('date');

        $summary = GetDailySummaryAction::resolve()->execute($staff, $date);

        return $this->resolveForSuccessResponseWith(
            message: 'Ringkasan harian berhasil diambil',
            data: $summary
        );
    }
}
