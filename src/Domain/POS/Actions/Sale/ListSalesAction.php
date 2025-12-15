<?php

namespace Domain\POS\Actions\Sale;

use Infra\POS\Models\Sale;
use Infra\Shared\Foundations\Action;

class ListSalesAction extends Action
{
    public function execute(array $filters = [])
    {
        $query = Sale::with(['items', 'payments', 'customer'])
            ->orderBy('sold_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('sold_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('sold_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['invoice_no'])) {
            $query->where('invoice_no', 'like', '%' . $filters['invoice_no'] . '%');
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }
}
