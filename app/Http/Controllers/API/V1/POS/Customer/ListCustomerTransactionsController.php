<?php

namespace App\Http\Controllers\API\V1\POS\Customer;

use Domain\POS\Actions\Customer\ListLoyaltyTransactionsAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\POS\Models\Customer;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class ListCustomerTransactionsController extends BaseController
{
    public function __invoke(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'type' => 'nullable|string|in:earn,redeem,adjust',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $paginator = ListLoyaltyTransactionsAction::resolve()->execute($customer, $request->only([
                'type',
                'per_page',
            ]));

            return $this->resolveForSuccessResponseWithPage(
                message: 'Riwayat loyalti pelanggan berhasil diambil',
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
