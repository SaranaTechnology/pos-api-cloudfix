<?php

namespace Domain\SelfOrder\Actions;

use Infra\SelfOrder\Models\SelfOrder;

class ShowSelfOrderStatusAction
{
    public function execute(string $orderNo)
    {
        $order = SelfOrder::where('order_no', $orderNo)
            ->with('items')
            ->firstOrFail();

        return [
            'order_no' => $order->order_no,
            'customer_name' => $order->customer_name,
            'table_no' => $order->table_no,
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
            'total' => $order->total,
            'total_formatted' => $order->total_formatted,
            'items' => $order->items->map(fn($item) => [
                'item_name' => $item->item_name,
                'qty' => $item->qty,
                'price' => $item->price,
                'line_total' => $item->line_total,
                'notes' => $item->notes,
            ]),
            'created_at' => $order->created_at,
        ];
    }
}
