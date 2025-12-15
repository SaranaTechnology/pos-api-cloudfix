<?php

namespace Domain\POS\Actions;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Infra\POS\Models\Customer;
use Infra\POS\Models\LoyaltyTransaction;
use Infra\POS\Models\Payment;
use Infra\POS\Models\Sale;
use Infra\POS\Models\SaleItem;
use Infra\Produksi\Models\Product;
use Infra\Accounting\Models\JournalEntry;
use Infra\Accounting\Models\JournalLine;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateSaleAction extends Action
{
    public function execute(array $data)
    {
        if (!Arr::exists($data, 'items') || !is_array($data['items']) || empty($data['items'])) {
            throw new BadRequestException('items wajib diisi (array of {product_id, qty, price})');
        }

        if (!Arr::exists($data, 'payment')) {
            throw new BadRequestException('payment wajib diisi');
        }

        $this->validateItems($data['items']);

        $soldAt = Carbon::parse(Arr::get($data, 'sold_at', Carbon::now()));
        $invoice = Arr::get($data, 'invoice_no', 'POS-' . Str::upper(Str::random(10)));
        $subtotal = $this->calculateSubtotal($data['items']);
        $discount = (int) Arr::get($data, 'discount', 0);
        $tax = (int) Arr::get($data, 'tax', 0);
        $total = $subtotal - $discount + $tax;

        $sale = Sale::create([
            'invoice_no' => $invoice,
            'customer_id' => Arr::get($data, 'customer_id'),
            'sold_at' => $soldAt,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'status' => 'paid',
        ]);

        $this->createSaleItems($sale, $data['items']);
        $this->createPayment($sale, $data['payment'], $total);
        $this->createJournalEntry($soldAt, $invoice, $total);
        $this->processLoyalty($sale, $data, $total);

        return $sale->load('items', 'payments', 'customer');
    }

    protected function validateItems(array $items): void
    {
        foreach ($items as $item) {
            foreach (['product_id', 'qty', 'price'] as $key) {
                if (!Arr::exists($item, $key)) {
                    throw new BadRequestException("$key wajib di item");
                }
            }
        }
    }

    protected function calculateSubtotal(array $items): int
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (int) ($item['qty'] * $item['price']);
        }
        return $subtotal;
    }

    protected function createSaleItems(Sale $sale, array $items): void
    {
        foreach ($items as $item) {
            $lineTotal = (int) ($item['qty'] * $item['price']);
            SaleItem::create([
                'pos_sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'line_total' => $lineTotal,
            ]);

            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new BadRequestException('Produk tidak ditemukan: ' . $item['product_id']);
            }

            $product->stok = max(0, ($product->stok - $item['qty']));
            $product->save();
        }
    }

    protected function createPayment(Sale $sale, array $payment, int $total): void
    {
        Payment::create([
            'pos_sale_id' => $sale->id,
            'method' => Arr::get($payment, 'method', 'cash'),
            'amount' => Arr::get($payment, 'amount', $total),
            'reference' => Arr::get($payment, 'reference'),
        ]);
    }

    protected function createJournalEntry(Carbon $soldAt, string $invoice, int $total): void
    {
        $cashCoa = env('POS_CASH_ACCOUNT_ID');
        $salesCoa = env('POS_SALES_ACCOUNT_ID');

        if (!$cashCoa || !$salesCoa) {
            return;
        }

        $entry = JournalEntry::create([
            'date' => $soldAt->toDateString(),
            'reference' => $invoice,
            'memo' => 'POS Sale',
            'source' => 'pos',
            'status' => 'Posted',
        ]);

        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $cashCoa,
            'debit' => $total,
            'credit' => 0,
            'memo' => 'Cash in POS',
        ]);

        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $salesCoa,
            'debit' => 0,
            'credit' => $total,
            'memo' => 'Sales revenue',
        ]);
    }

    protected function processLoyalty(Sale $sale, array $data, int $total): void
    {
        if (!$sale->customer_id) {
            return;
        }

        $customer = Customer::find($sale->customer_id);
        if (!$customer) {
            return;
        }

        $customer->lifetime_value = (int) $customer->lifetime_value + $total;

        $loyaltyData = Arr::get($data, 'loyalty', []);
        $awardPoints = (int) Arr::get($loyaltyData, 'points', 0);

        if ($awardPoints > 0) {
            $newBalance = $customer->loyalty_points + $awardPoints;
            LoyaltyTransaction::create([
                'customer_id' => $customer->id,
                'sale_id' => $sale->id,
                'type' => 'earn',
                'points' => $awardPoints,
                'balance_after' => $newBalance,
                'description' => Arr::get($loyaltyData, 'description', 'Poin dari transaksi POS ' . $sale->invoice_no),
            ]);
            $customer->loyalty_points = $newBalance;
        }

        $customer->save();
    }
}
