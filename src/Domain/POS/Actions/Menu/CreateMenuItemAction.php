<?php

namespace Domain\POS\Actions\Menu;

use Illuminate\Support\Arr;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateMenuItemAction extends Action
{
    public function execute(array $payload): MenuItem
    {
        $price = (int) Arr::get($payload, 'price', 0);
        if ($price < 0) {
            throw new BadRequestException('Harga menu tidak boleh negatif');
        }

        $loyaltyPoints = (int) Arr::get($payload, 'loyalty_points', 0);
        if ($loyaltyPoints < 0) {
            throw new BadRequestException('Poin loyalti tidak boleh negatif');
        }

        try {
            return MenuItem::create([
                'product_id' => Arr::get($payload, 'product_id'),
                'sku' => Arr::get($payload, 'sku'),
                'name' => Arr::get($payload, 'name'),
                'description' => Arr::get($payload, 'description'),
                'price' => $price,
                'is_active' => Arr::has($payload, 'is_active')
                    ? (bool) Arr::get($payload, 'is_active')
                    : true,
                'loyalty_points' => $loyaltyPoints,
                'metadata' => Arr::get($payload, 'metadata'),
            ]);
        } catch (\Throwable $th) {
            throw new BadRequestException($th->getMessage());
        }
    }
}
