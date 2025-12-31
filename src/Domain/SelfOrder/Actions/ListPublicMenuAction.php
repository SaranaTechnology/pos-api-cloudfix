<?php

namespace Domain\SelfOrder\Actions;

use Infra\POS\Models\MenuItem;

class ListPublicMenuAction
{
    public function execute(array $data = [])
    {
        $query = MenuItem::query()
            ->with('category:id,name,slug')
            ->where('is_active', true)
            ->orderBy('name');

        // Filter by category_id (new FK relationship)
        if (!empty($data['category_id'])) {
            $query->where('category_id', $data['category_id']);
        }

        // Legacy: Filter by category string field
        if (!empty($data['category'])) {
            $query->where('category', $data['category']);
        }

        if (!empty($data['search'])) {
            $query->where('name', 'like', '%' . $data['search'] . '%');
        }

        // Support pagination
        if (!empty($data['per_page'])) {
            return $query->paginate($data['per_page']);
        }

        return $query->get();
    }
}
