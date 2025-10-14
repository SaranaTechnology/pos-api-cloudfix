<?php

namespace App\Http\Controllers\API\V1\POS\Customer;

use Domain\POS\Actions\Customer\DeleteCustomerAction;
use Infra\POS\Models\Customer;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class DeleteCustomerController extends BaseController
{
    public function __invoke(Customer $customer)
    {
        try {
            DeleteCustomerAction::resolve()->execute($customer);

            return $this->resolveForSuccessResponseWith(
                message: 'Pelanggan POS berhasil dihapus',
                data: null,
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
