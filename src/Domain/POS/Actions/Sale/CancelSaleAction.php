<?php

namespace Domain\POS\Actions\Sale;

use Infra\POS\Models\Sale;
use Infra\Produksi\Models\Product;
use Infra\Shared\Foundations\Action;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CancelSaleAction extends Action
{
    public function execute(int $id, array $data = [])
    {
        $sale = Sale::with('items')->find($id);

        if (!$sale) {
            throw new BadRequestException('Transaksi tidak ditemukan');
        }

        if ($sale->status === 'cancelled') {
            throw new BadRequestException('Transaksi sudah dibatalkan');
        }

        return DB::transaction(function () use ($sale, $data) {
            foreach ($sale->items as $item) {
                if (!$item->product_id) {
                    continue;
                }

                $product = Product::find($item->product_id);
                if (!$product) {
                    continue;
                }

                $product->stok = $product->stok + $item->qty;
                $product->save();
            }

            $sale->status = 'cancelled';
            $sale->notes = $data['reason'] ?? 'Dibatalkan';
            $sale->save();

            return $sale->load(['items', 'payments', 'customer']);
        });
    }
}
