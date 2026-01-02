<?php

namespace Domain\Cashier\Actions;

use Infra\Cashier\Models\CashierShift;
use Infra\Shared\Foundations\Action;
use Infra\Staff\Models\Staff;

class GetCurrentShiftAction extends Action
{
    public function execute(Staff $staff): ?CashierShift
    {
        return CashierShift::forStaff($staff->id)->open()->first();
    }
}
