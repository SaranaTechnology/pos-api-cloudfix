<?php

namespace Domain\POS\Actions\Menu;

use Illuminate\Support\Arr;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdateMenuItemAction extends Action
{
    public function execute(MenuItem $menuItem, array $payload): MenuItem
    {
        $price = Arr::has($payload, 'price')
            ? (int) Arr::get($payload, 'price', $menuItem->price)
            : $menuItem->price;
        if ($price < 0) {
            throw new BadRequestException('Harga menu tidak boleh negatif');
        }

        $loyaltyPoints = Arr::has($payload, 'loyalty_points')
            ? (int) Arr::get($payload, 'loyalty_points', $menuItem->loyalty_points)
            : $menuItem->loyalty_points;
        if ($loyaltyPoints < 0) {
            throw new BadRequestException('Poin loyalti tidak boleh negatif');
        }

        $menuItem->fill([
            'product_id' => Arr::get($payload, 'product_id', $menuItem->product_id),
            'sku' => Arr::get($payload, 'sku', $menuItem->sku),
            'name' => Arr::get($payload, 'name', $menuItem->name),
            'description' => Arr::get($payload, 'description', $menuItem->description),
            'price' => $price,
            'is_active' => Arr::has($payload, 'is_active')
                ? (bool) Arr::get($payload, 'is_active')
                : $menuItem->is_active,
            'loyalty_points' => $loyaltyPoints,
            'metadata' => Arr::get($payload, 'metadata', $menuItem->metadata),
        ]);
        $menuItem->save();

        return $menuItem->fresh();
    }
}
