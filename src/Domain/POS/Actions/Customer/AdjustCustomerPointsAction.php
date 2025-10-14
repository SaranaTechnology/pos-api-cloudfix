<?php

namespace Domain\POS\Actions\Customer;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Infra\POS\Models\Customer;
use Infra\POS\Models\LoyaltyTransaction;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AdjustCustomerPointsAction extends Action
{
    public function execute(Customer $customer, array $payload): LoyaltyTransaction
    {
        $type = strtolower((string) Arr::get($payload, 'type', 'earn'));
        $validTypes = ['earn', 'redeem', 'adjust'];
        if (! in_array($type, $validTypes, true)) {
            throw new BadRequestException('Tipe transaksi loyalti tidak valid');
        }

        $points = (int) Arr::get($payload, 'points', 0);
        if ($points <= 0) {
            throw new BadRequestException('Points harus lebih besar dari 0');
        }

        $multiplier = 1;
        if ($type === 'redeem') {
            $multiplier = -1;
        } elseif ($type === 'adjust') {
            $multiplier = (int) Arr::get($payload, 'direction', 1);
            $multiplier = $multiplier < 0 ? -1 : 1;
        }

        $delta = $points * $multiplier;
        $balanceAfter = $customer->loyalty_points + $delta;
        if ($balanceAfter < 0) {
            throw new BadRequestException('Poin loyalti tidak mencukupi untuk diredeem');
        }

        return DB::transaction(function () use ($customer, $payload, $type, $points, $delta, $balanceAfter) {
            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customer->id,
                'sale_id' => Arr::get($payload, 'sale_id'),
                'type' => $type,
                'points' => $delta,
                'balance_after' => $balanceAfter,
                'description' => Arr::get($payload, 'description'),
            ]);

            $customer->loyalty_points = $balanceAfter;
            $customer->lifetime_value = $customer->lifetime_value + max(0, $delta);
            $customer->save();

            return $transaction->load('customer');
        });
    }
}
