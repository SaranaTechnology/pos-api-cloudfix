<?php

namespace App\Http\Controllers\API\V1\Kitchen;

use Domain\SelfOrder\Actions\UpdateOrderStatusAction;
use Infra\SelfOrder\Enums\SelfOrderStatus;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class ReadyOrderController extends BaseController
{
    public function __invoke(string $orderNo, UpdateOrderStatusAction $action)
    {
        try {
            $order = $action->execute($orderNo, SelfOrderStatus::READY);

            return $this->resolveForSuccessResponseWith('Order is ready for pickup', $order);
        } catch (\InvalidArgumentException $e) {
            return $this->resolveForFailedResponseWith($e->getMessage(), [], HttpStatus::BadRequest);
        }
    }
}
