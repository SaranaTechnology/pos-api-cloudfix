<?php

namespace Domain\SelfOrder\Actions;

use Infra\SelfOrder\Enums\SelfOrderStatus;
use Infra\SelfOrder\Models\SelfOrder;

class IndexKitchenOrdersAction
{
    public function execute(array $filters = [])
    {
        $query = SelfOrder::with('items')
            ->orderBy('created_at', 'asc');

        // Default: show pending, confirmed, preparing orders
        $statuses = $filters['statuses'] ?? [
            SelfOrderStatus::PENDING->value,
            SelfOrderStatus::CONFIRMED->value,
            SelfOrderStatus::PREPARING->value,
        ];

        $query->whereIn('status', $statuses);

        if (!empty($filters['table_no'])) {
            $query->where('table_no', $filters['table_no']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        } else {
            // Default to today
            $query->whereDate('created_at', now()->toDateString());
        }

        $perPage = $filters['per_page'] ?? 50;

        return $query->paginate($perPage);
    }
}
