<?php

namespace Domain\SelfOrder\Actions;

use Illuminate\Support\Facades\DB;
use Infra\POS\Models\Combo;
use Infra\POS\Models\MenuItem;
use Infra\SelfOrder\Enums\SelfOrderStatus;
use Infra\SelfOrder\Models\SelfOrder;
use Infra\SelfOrder\Models\SelfOrderItem;

class CreateSelfOrderAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                $itemData = $this->resolveItem($item);
                $lineTotal = $itemData['price'] * $item['qty'];
                $subtotal += $lineTotal;

                $orderItems[] = [
                    'menu_item_id' => $itemData['menu_item_id'],
                    'combo_id' => $itemData['combo_id'],
                    'item_name' => $itemData['name'],
                    'qty' => $item['qty'],
                    'price' => $itemData['price'],
                    'line_total' => $lineTotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $tax = (int) ceil($subtotal * 0.11); // PPN 11%
            $total = $subtotal + $tax;

            $order = SelfOrder::create([
                'order_no' => SelfOrder::generateOrderNo(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'table_no' => $data['table_no'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => SelfOrderStatus::PENDING,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($orderItems as $orderItem) {
                $order->items()->create($orderItem);
            }

            return $order->load('items');
        });
    }

    private function resolveItem(array $item): array
    {
        if (!empty($item['menu_item_id'])) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);

            return [
                'menu_item_id' => $menuItem->id,
                'combo_id' => null,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
            ];
        }

        if (!empty($item['combo_id'])) {
            $combo = Combo::findOrFail($item['combo_id']);

            return [
                'menu_item_id' => null,
                'combo_id' => $combo->id,
                'name' => $combo->name,
                'price' => $combo->price,
            ];
        }

        throw new \InvalidArgumentException('Item harus memiliki menu_item_id atau combo_id');
    }
}
