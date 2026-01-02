<?php

namespace Domain\Cashier\Actions;

use Infra\Cashier\Models\CashierShift;
use Infra\POS\Models\Sale;
use Infra\Shared\Foundations\Action;
use Infra\Staff\Models\Staff;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CloseShiftAction extends Action
{
    public function execute(Staff $staff, array $data): CashierShift
    {
        $shift = CashierShift::forStaff($staff->id)->open()->first();

        if (!$shift) {
            throw new BadRequestException('Tidak ada shift yang terbuka.');
        }

        // Calculate sales during this shift
        $sales = Sale::where('created_at', '>=', $shift->opened_at)
            ->where('created_at', '<=', now())
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalTransactions = $sales->count();
        $totalSales = $sales->sum('total');
        $totalCashSales = $sales->where('payment_method', 'cash')->sum('total');
        $totalNonCashSales = $totalSales - $totalCashSales;

        $closingCash = $data['closing_cash'] ?? 0;
        $expectedCash = $shift->opening_cash + $totalCashSales;
        $cashDifference = $closingCash - $expectedCash;

        $shift->update([
            'closed_at' => now(),
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $cashDifference,
            'total_transactions' => $totalTransactions,
            'total_sales' => $totalSales,
            'total_cash_sales' => $totalCashSales,
            'total_non_cash_sales' => $totalNonCashSales,
            'notes' => $data['notes'] ?? $shift->notes,
            'status' => 'closed',
        ]);

        return $shift->fresh();
    }
}
