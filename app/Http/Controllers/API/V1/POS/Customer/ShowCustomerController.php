<?php

namespace App\Http\Controllers\API\V1\POS\Customer;

use Domain\POS\Actions\Customer\ShowCustomerAction;
use Infra\POS\Models\Customer;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class ShowCustomerController extends BaseController
{
    public function __invoke(Customer $customer)
    {
        try {
            $customer = ShowCustomerAction::resolve()->execute($customer);

            return $this->resolveForSuccessResponseWith(
                message: 'Detail pelanggan POS berhasil diambil',
                data: $customer,
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
