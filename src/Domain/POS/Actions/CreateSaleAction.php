<?php

namespace Domain\POS\Actions;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
        // Validate basics
        if (! Arr::exists($data, 'items') || ! is_array($data['items']) || empty($data['items'])) {
            throw new BadRequestException('items wajib diisi (array of {product_id, qty, price})');
        }
        if (! Arr::exists($data, 'payment')) {
            throw new BadRequestException('payment wajib diisi');
        }

        $soldAt = Carbon::parse(Arr::get($data, 'sold_at', Carbon::now()));
        $invoice = Arr::get($data, 'invoice_no', 'POS-'.Str::upper(Str::random(10)));

        // Compute totals
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            foreach (['product_id', 'qty', 'price'] as $key) {
                if (! Arr::exists($item, $key)) {
                    throw new BadRequestException("$key wajib di item");
                }
            }
            $subtotal += (int) ($item['qty'] * $item['price']);
        }
        $discount = (int) Arr::get($data, 'discount', 0);
        $tax = (int) Arr::get($data, 'tax', 0);
        $total = $subtotal - $discount + $tax;

        // Create Sale
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

        // Create Items and update stock
        foreach ($data['items'] as $item) {
            $lineTotal = (int) ($item['qty'] * $item['price']);
            SaleItem::create([
                'pos_sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'line_total' => $lineTotal,
            ]);

            $product = Product::find($item['product_id']);
            if (! $product) {
                throw new BadRequestException('Produk tidak ditemukan: '.$item['product_id']);
            }
            $product->stok = max(0, ($product->stok - $item['qty']));
            $product->save();
        }

        // Record payment
        $payment = $data['payment'];
        Payment::create([
            'pos_sale_id' => $sale->id,
            'method' => Arr::get($payment, 'method', 'cash'),
            'amount' => Arr::get($payment, 'amount', $total),
            'reference' => Arr::get($payment, 'reference'),
        ]);

        // Optional: create accounting journal if mapping provided via env
        $cashCoa = env('POS_CASH_ACCOUNT_ID');
        $salesCoa = env('POS_SALES_ACCOUNT_ID');
        if ($cashCoa && $salesCoa) {
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

        return $sale->load('items', 'payments');
    }
}

