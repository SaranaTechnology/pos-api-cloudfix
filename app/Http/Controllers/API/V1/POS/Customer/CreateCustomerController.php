<?php

namespace App\Http\Controllers\API\V1\POS\Customer;

use Domain\POS\Actions\Customer\CreateCustomerAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateCustomerController extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:30',
                'email' => 'nullable|email|max:255',
                'loyalty_points' => 'nullable|integer|min:0',
                'lifetime_value' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
            ]);

            $customer = CreateCustomerAction::resolve()->execute($request->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Pelanggan POS berhasil dibuat',
                data: $customer,
                status: HttpStatus::Created
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
