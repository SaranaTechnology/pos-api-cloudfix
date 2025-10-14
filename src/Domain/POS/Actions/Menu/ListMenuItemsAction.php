<?php

namespace Domain\POS\Actions\Menu;

use Illuminate\Support\Arr;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Foundations\Action;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListMenuItemsAction extends Action
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = MenuItem::query();

        $search = trim((string) Arr::get($filters, 'search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (Arr::has($filters, 'is_active')) {
            $query->where('is_active', (bool) Arr::get($filters, 'is_active'));
        }

        $perPage = (int) Arr::get($filters, 'per_page', 15);
        $perPage = $perPage > 0 ? $perPage : 15;

        return $query
            ->orderBy('name')
            ->paginate($perPage);
    }
}
