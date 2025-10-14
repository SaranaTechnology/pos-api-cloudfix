<?php

namespace Domain\POS\Actions\Customer;

use Illuminate\Support\Arr;
use Infra\POS\Models\Customer;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdateCustomerAction extends Action
{
    public function execute(Customer $customer, array $payload): Customer
    {
        if (Arr::has($payload, 'loyalty_points')) {
            $points = (int) Arr::get($payload, 'loyalty_points', $customer->loyalty_points);
            if ($points < 0) {
                throw new BadRequestException('Poin loyalti tidak boleh negatif');
            }
        }

        $customer->fill([
            'name' => Arr::get($payload, 'name', $customer->name),
            'phone' => Arr::get($payload, 'phone', $customer->phone),
            'email' => Arr::get($payload, 'email', $customer->email),
            'loyalty_points' => Arr::has($payload, 'loyalty_points')
                ? (int) Arr::get($payload, 'loyalty_points', $customer->loyalty_points)
                : $customer->loyalty_points,
            'lifetime_value' => Arr::has($payload, 'lifetime_value')
                ? (int) Arr::get($payload, 'lifetime_value', $customer->lifetime_value)
                : $customer->lifetime_value,
            'metadata' => Arr::get($payload, 'metadata', $customer->metadata),
        ]);
        $customer->save();

        return $customer->fresh();
    }
}
