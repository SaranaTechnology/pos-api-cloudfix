<?php

namespace Domain\SelfOrder\Actions;

use Infra\POS\Models\MenuItem;

class ListPublicMenuAction
{
    public function execute(array $data = [])
    {
        $query = MenuItem::query()
            ->where('is_active', true)
            ->orderBy('name');

        if (!empty($data['category'])) {
            $query->where('category', $data['category']);
        }

        if (!empty($data['search'])) {
            $query->where('name', 'like', '%' . $data['search'] . '%');
        }

        return $query->get([
            'id',
            'name',
            'description',
            'price',
            'category',
            'image_url',
            'metadata',
        ]);
    }
}
