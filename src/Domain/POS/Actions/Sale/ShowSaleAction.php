<?php

namespace Domain\POS\Actions\Sale;

use Infra\POS\Models\Sale;
use Infra\Shared\Foundations\Action;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ShowSaleAction extends Action
{
    public function execute(int $id)
    {
        $sale = Sale::with(['items', 'payments', 'customer'])->find($id);

        if (!$sale) {
            throw new BadRequestException('Transaksi tidak ditemukan');
        }

        return $sale;
    }
}
