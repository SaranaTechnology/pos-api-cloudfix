<?php

namespace Domain\POS\Actions\Combo;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Infra\POS\Models\Combo;
use Infra\Shared\Foundations\Action;

class ListCombosAction extends Action
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = Combo::query()->with('items');

        $search = trim((string) Arr::get($filters, 'search', ''));
        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
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
