<?php

namespace App\Http\Controllers\API\V1\POS\Sale;

use Domain\POS\Actions\PostCogsForSaleAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\POS\Models\Sale;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PostCogsController extends BaseController
{
    public function __invoke(Sale $sale, Request $req)
    {
        try {
            $req->validate([
                'items_cost' => 'required|array',
                'items_cost.*.product_id' => 'sometimes|integer',
                'items_cost.*.cost' => 'sometimes|integer|min:0',
            ]);

            $data = PostCogsForSaleAction::resolve()->execute($sale, $req->all());
            return $this->resolveForSuccessResponseWith(
                message: 'Jurnal COGS/Inventory berhasil diposting',
                data: $data,
                status: HttpStatus::Ok
            );
        } catch (ValidationException $th) {
            return $this->resolveForFailedResponseWith(
                message: 'Validation Error',
                data: $th->errors(),
                status: HttpStatus::UnprocessableEntity
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

