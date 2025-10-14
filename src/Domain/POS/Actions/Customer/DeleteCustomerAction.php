<?php

namespace Domain\POS\Actions\Customer;

use Infra\POS\Models\Customer;
use Infra\Shared\Foundations\Action;

class DeleteCustomerAction extends Action
{
    public function execute(Customer $customer): void
    {
        $customer->delete();
    }
}
