<?php

namespace App\Http\Controllers\API\V1\POS\Sale;

use Domain\POS\Actions\Sale\ListSalesAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class IndexSaleController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {
            $filters = $req->only([
                'status',
                'customer_id',
                'from_date',
                'to_date',
                'invoice_no',
                'per_page',
            ]);

            $data = ListSalesAction::resolve()->execute($filters);

            return $this->resolveForSuccessResponseWith(
                message: 'Berhasil mengambil data transaksi',
                data: $data,
                status: HttpStatus::Ok
            );
        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage(),
                data: [],
                status: HttpStatus::InternalError
            );
        }
    }
}
