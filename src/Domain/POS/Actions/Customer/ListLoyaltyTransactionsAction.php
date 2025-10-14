<?php

namespace Domain\POS\Actions\Customer;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Infra\POS\Models\Customer;
use Infra\POS\Models\LoyaltyTransaction;
use Infra\Shared\Foundations\Action;

class ListLoyaltyTransactionsAction extends Action
{
    public function execute(Customer $customer, array $filters = []): LengthAwarePaginator
    {
        $query = LoyaltyTransaction::query()
            ->where('customer_id', $customer->id)
            ->latest();

        if ($type = Arr::get($filters, 'type')) {
            $query->where('type', $type);
        }

        $perPage = (int) Arr::get($filters, 'per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        return $query->paginate($perPage);
    }
}
