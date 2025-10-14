<?php

namespace Domain\POS\Actions\Combo;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Infra\POS\Models\Combo;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateComboAction extends Action
{
    public function execute(array $payload): Combo
    {
        $items = Arr::get($payload, 'items', []);
        if (! is_array($items) || empty($items)) {
            throw new BadRequestException('Items combo wajib diisi');
        }

        $price = (int) Arr::get($payload, 'price', 0);
        if ($price < 0) {
            throw new BadRequestException('Harga combo tidak boleh negatif');
        }

        $loyaltyPoints = (int) Arr::get($payload, 'loyalty_points', 0);
        if ($loyaltyPoints < 0) {
            throw new BadRequestException('Poin loyalti tidak boleh negatif');
        }

        $syncPayload = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                throw new BadRequestException('Format items combo tidak valid');
            }
            $menuItemId = Arr::get($item, 'menu_item_id');
            $quantity = (int) Arr::get($item, 'quantity', 1);
            if (empty($menuItemId)) {
                throw new BadRequestException('menu_item_id wajib diisi pada setiap item');
            }
            if ($quantity <= 0) {
                throw new BadRequestException('quantity untuk combo harus lebih dari 0');
            }
            $syncPayload[$menuItemId] = ['quantity' => $quantity];
        }

        $foundCount = MenuItem::whereIn('id', array_keys($syncPayload))->count();
        if ($foundCount !== count($syncPayload)) {
            throw new BadRequestException('Terdapat menu item yang tidak ditemukan');
        }

        return DB::transaction(function () use ($payload, $price, $loyaltyPoints, $syncPayload) {
            $combo = Combo::create([
                'name' => Arr::get($payload, 'name'),
                'description' => Arr::get($payload, 'description'),
                'price' => $price,
                'is_active' => Arr::has($payload, 'is_active')
                    ? (bool) Arr::get($payload, 'is_active')
                    : true,
                'loyalty_points' => $loyaltyPoints,
                'metadata' => Arr::get($payload, 'metadata'),
            ]);

            $combo->items()->sync($syncPayload);

            return $combo->load('items');
        });
    }
}
