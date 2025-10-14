<?php

namespace Domain\POS\Actions\Customer;

use Infra\POS\Models\Customer;
use Infra\Shared\Foundations\Action;

class ShowCustomerAction extends Action
{
    public function execute(Customer $customer): Customer
    {
        return $customer->load([
            'loyaltyTransactions' => function ($query) {
                $query->latest();
            },
        ]);
    }
}
