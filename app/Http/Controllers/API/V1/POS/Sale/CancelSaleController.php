<?php

namespace App\Http\Controllers\API\V1\POS\Sale;

use Domain\POS\Actions\Sale\CancelSaleAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CancelSaleController extends BaseController
{
    public function __invoke(Request $req, int $sale)
    {
        try {
            $data = CancelSaleAction::resolve()->execute($sale, $req->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Berhasil membatalkan transaksi',
                data: $data,
                status: HttpStatus::Ok
            );
        } catch (BadRequestException $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage(),
                data: [],
                status: HttpStatus::BadRequest
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
