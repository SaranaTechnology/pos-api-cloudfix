<?php

namespace App\Http\Controllers\API\V1\POS\Customer;

use Domain\POS\Actions\Customer\UpdateCustomerAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\POS\Models\Customer;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdateCustomerController extends BaseController
{
    public function __invoke(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255',
                'loyalty_points' => 'nullable|integer|min:0',
                'lifetime_value' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
            ]);

            $customer = UpdateCustomerAction::resolve()->execute($customer, $request->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Pelanggan POS berhasil diperbarui',
                data: $customer,
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
