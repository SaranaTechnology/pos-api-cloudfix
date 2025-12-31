<?php

namespace App\Http\Controllers\API\V1\Kitchen;

use Domain\SelfOrder\Actions\UpdateOrderStatusAction;
use Infra\SelfOrder\Enums\SelfOrderStatus;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class CompleteOrderController extends BaseController
{
    public function __invoke(string $orderNo, UpdateOrderStatusAction $action)
    {
        try {
            $order = $action->execute($orderNo, SelfOrderStatus::COMPLETED);

            return $this->resolveForSuccessResponseWith('Order completed', $order);
        } catch (\InvalidArgumentException $e) {
            return $this->resolveForFailedResponseWith($e->getMessage(), [], HttpStatus::BadRequest);
        }
    }
}
