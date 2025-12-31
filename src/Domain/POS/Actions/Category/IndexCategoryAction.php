<?php

namespace Domain\POS\Actions\Category;

use Infra\POS\Models\Category;

class IndexCategoryAction
{
    public function execute(array $filters = [])
    {
        $query = Category::query()->ordered();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->withCount('activeMenuItems as items_count')->paginate($perPage);
    }
}
