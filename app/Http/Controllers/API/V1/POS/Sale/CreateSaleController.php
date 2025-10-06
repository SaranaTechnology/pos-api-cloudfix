<?php

namespace App\Http\Controllers\API\V1\POS\Sale;

use Domain\POS\Actions\CreateSaleAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateSaleController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {
            $req->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.price' => 'required|integer|min:0',
                'payment' => 'required|array',
                'payment.method' => 'required|string',
                'payment.amount' => 'nullable|integer|min:0',
                'discount' => 'nullable|integer|min:0',
                'tax' => 'nullable|integer|min:0',
                'invoice_no' => 'nullable|string',
                'sold_at' => 'nullable|date',
                'customer_id' => 'nullable|integer',
            ]);

            $data = CreateSaleAction::resolve()->execute($req->all());
            return $this->resolveForSuccessResponseWith(
                message: 'Transaksi POS berhasil',
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

