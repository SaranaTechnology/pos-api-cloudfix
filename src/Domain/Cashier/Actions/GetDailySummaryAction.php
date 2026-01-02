<?php

namespace Domain\Cashier\Actions;

use Carbon\Carbon;
use Infra\POS\Models\Sale;
use Infra\Shared\Foundations\Action;
use Infra\Staff\Models\Staff;

class GetDailySummaryAction extends Action
{
    public function execute(Staff $staff, ?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $sales = Sale::whereDate('created_at', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalTransactions = $sales->count();
        $totalSales = $sales->sum('total');
        $totalCashSales = $sales->where('payment_method', 'cash')->sum('total');
        $totalNonCashSales = $totalSales - $totalCashSales;
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Get sales by payment method
        $salesByPaymentMethod = $sales->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        // Get top selling items
        $topItems = $sales->flatMap(function ($sale) {
            return $sale->items ?? [];
        })->groupBy('product_id')->map(function ($group) {
            $first = $group->first();
            return [
                'product_id' => $first['product_id'] ?? null,
                'product_name' => $first['product_name'] ?? 'Unknown',
                'quantity' => $group->sum('qty'),
                'total' => $group->sum(fn($item) => ($item['qty'] ?? 0) * ($item['price'] ?? 0)),
            ];
        })->sortByDesc('quantity')->take(10)->values();

        return [
            'date' => $date->toDateString(),
            'total_transactions' => $totalTransactions,
            'total_sales' => $totalSales,
            'total_cash_sales' => $totalCashSales,
            'total_non_cash_sales' => $totalNonCashSales,
            'average_transaction' => round($averageTransaction, 2),
            'sales_by_payment_method' => $salesByPaymentMethod,
            'top_items' => $topItems,
        ];
    }
}
