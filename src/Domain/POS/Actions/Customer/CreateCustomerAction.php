<?php

namespace Domain\POS\Actions\Customer;

use Illuminate\Support\Arr;
use Infra\POS\Models\Customer;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateCustomerAction extends Action
{
    public function execute(array $payload): Customer
    {
        $loyaltyPoints = (int) Arr::get($payload, 'loyalty_points', 0);
        if ($loyaltyPoints < 0) {
            throw new BadRequestException('Poin awal tidak boleh negatif');
        }

        return Customer::create([
            'name' => Arr::get($payload, 'name'),
            'phone' => Arr::get($payload, 'phone'),
            'email' => Arr::get($payload, 'email'),
            'loyalty_points' => $loyaltyPoints,
            'lifetime_value' => (int) Arr::get($payload, 'lifetime_value', 0),
            'metadata' => Arr::get($payload, 'metadata'),
        ]);
    }
}
