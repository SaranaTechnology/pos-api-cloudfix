<?php

namespace Domain\POS\Actions\Customer;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Infra\POS\Models\Customer;
use Infra\Shared\Foundations\Action;

class ListCustomersAction extends Action
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = Customer::query();

        $search = trim((string) Arr::get($filters, 'search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = (int) Arr::get($filters, 'per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        return $query
            ->orderBy('name')
            ->paginate($perPage);
    }
}
