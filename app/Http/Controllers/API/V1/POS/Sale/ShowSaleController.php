<?php

namespace App\Http\Controllers\API\V1\POS\Sale;

use Domain\POS\Actions\Sale\ShowSaleAction;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ShowSaleController extends BaseController
{
    public function __invoke(int $sale)
    {
        try {
            $data = ShowSaleAction::resolve()->execute($sale);

            return $this->resolveForSuccessResponseWith(
                message: 'Berhasil mengambil detail transaksi',
                data: $data,
                status: HttpStatus::Ok
            );
        } catch (BadRequestException $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage(),
                data: [],
                status: HttpStatus::NotFound
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
