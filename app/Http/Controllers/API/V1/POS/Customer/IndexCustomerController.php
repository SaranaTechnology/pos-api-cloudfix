<?php

namespace App\Http\Controllers\API\V1\POS\Customer;

use Domain\POS\Actions\Customer\ListCustomersAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class IndexCustomerController extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $paginator = ListCustomersAction::resolve()->execute($request->only([
                'search',
                'per_page',
            ]));

            return $this->resolveForSuccessResponseWithPage(
                message: 'Daftar pelanggan POS berhasil diambil',
                data: $paginator,
                status: HttpStatus::Ok
            );
        } catch (ValidationException $th) {
            return $this->resolveForFailedResponseWith(
                message: 'Validation Error',
                data: $th->errors(),
                status: HttpStatus::UnprocessableEntity
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
