<?php

namespace Domain\Cashier\Actions;

use Infra\Cashier\Models\CashierShift;
use Infra\Shared\Foundations\Action;
use Infra\Staff\Models\Staff;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OpenShiftAction extends Action
{
    public function execute(Staff $staff, array $data): CashierShift
    {
        // Check if staff already has an open shift
        $existingShift = CashierShift::forStaff($staff->id)->open()->first();

        if ($existingShift) {
            throw new BadRequestException('Anda masih memiliki shift yang terbuka. Tutup shift terlebih dahulu.');
        }

        $shift = CashierShift::create([
            'staff_id' => $staff->id,
            'staff_nip' => $staff->nip,
            'staff_name' => $staff->nama,
            'opened_at' => now(),
            'opening_cash' => $data['opening_cash'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'status' => 'open',
        ]);

        return $shift;
    }
}
