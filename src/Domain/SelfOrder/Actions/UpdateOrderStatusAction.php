<?php

namespace Domain\SelfOrder\Actions;

use Infra\SelfOrder\Enums\SelfOrderStatus;
use Infra\SelfOrder\Models\SelfOrder;

class UpdateOrderStatusAction
{
    public function execute(string $orderNo, SelfOrderStatus $newStatus): SelfOrder
    {
        $order = SelfOrder::where('order_no', $orderNo)->firstOrFail();

        $this->validateTransition($order->status, $newStatus);

        $order->update(['status' => $newStatus]);

        return $order->fresh()->load('items');
    }

    private function validateTransition(SelfOrderStatus $current, SelfOrderStatus $new): void
    {
        $allowedTransitions = [
            SelfOrderStatus::PENDING->value => [
                SelfOrderStatus::CONFIRMED->value,
                SelfOrderStatus::CANCELLED->value,
            ],
            SelfOrderStatus::CONFIRMED->value => [
                SelfOrderStatus::PREPARING->value,
                SelfOrderStatus::CANCELLED->value,
            ],
            SelfOrderStatus::PREPARING->value => [
                SelfOrderStatus::READY->value,
                SelfOrderStatus::CANCELLED->value,
            ],
            SelfOrderStatus::READY->value => [
                SelfOrderStatus::COMPLETED->value,
                SelfOrderStatus::CANCELLED->value,
            ],
            SelfOrderStatus::COMPLETED->value => [],
            SelfOrderStatus::CANCELLED->value => [],
        ];

        if (!in_array($new->value, $allowedTransitions[$current->value] ?? [])) {
            throw new \InvalidArgumentException(
                "Cannot transition from {$current->label()} to {$new->label()}"
            );
        }
    }
}
