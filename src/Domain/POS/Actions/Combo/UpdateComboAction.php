<?php

namespace Domain\POS\Actions\Combo;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Infra\POS\Models\Combo;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdateComboAction extends Action
{
    public function execute(Combo $combo, array $payload): Combo
    {
        $price = Arr::has($payload, 'price')
            ? (int) Arr::get($payload, 'price', $combo->price)
            : $combo->price;
        if ($price < 0) {
            throw new BadRequestException('Harga combo tidak boleh negatif');
        }

        $loyaltyPoints = Arr::has($payload, 'loyalty_points')
            ? (int) Arr::get($payload, 'loyalty_points', $combo->loyalty_points)
            : $combo->loyalty_points;
        if ($loyaltyPoints < 0) {
            throw new BadRequestException('Poin loyalti tidak boleh negatif');
        }

        $syncPayload = null;
        if (Arr::has($payload, 'items')) {
            $items = Arr::get($payload, 'items', []);
            if (! is_array($items) || empty($items)) {
                throw new BadRequestException('Items combo wajib diisi ketika mengubah daftar item');
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
        }

        return DB::transaction(function () use ($combo, $payload, $price, $loyaltyPoints, $syncPayload) {
            $combo->fill([
                'name' => Arr::get($payload, 'name', $combo->name),
                'description' => Arr::get($payload, 'description', $combo->description),
                'price' => $price,
                'is_active' => Arr::has($payload, 'is_active')
                    ? (bool) Arr::get($payload, 'is_active')
                    : $combo->is_active,
                'loyalty_points' => $loyaltyPoints,
                'metadata' => Arr::get($payload, 'metadata', $combo->metadata),
            ]);
            $combo->save();

            if (is_array($syncPayload)) {
                $combo->items()->sync($syncPayload);
            }

            return $combo->load('items');
        });
    }
}
