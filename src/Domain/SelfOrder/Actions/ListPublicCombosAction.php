<?php

namespace Domain\SelfOrder\Actions;

use Infra\POS\Models\Combo;

class ListPublicCombosAction
{
    public function execute(array $data = [])
    {
        return Combo::query()
            ->where('is_active', true)
            ->with(['items:id,name,price,image_url'])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'description',
                'price',
                'image_url',
                'metadata',
            ]);
    }
}
