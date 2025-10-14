<?php

namespace App\Http\Controllers\API\V1\POS\Customer;

use Domain\POS\Actions\Customer\AdjustCustomerPointsAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\POS\Models\Customer;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AdjustCustomerPointsController extends BaseController
{
    public function __invoke(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:earn,redeem,adjust',
                'points' => 'required|integer|min:1',
                'sale_id' => 'nullable|integer',
                'description' => 'nullable|string|max:255',
                'direction' => 'nullable|integer|in:-1,1',
            ]);

            $transaction = AdjustCustomerPointsAction::resolve()->execute($customer, $request->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Transaksi loyalti pelanggan berhasil dicatat',
                data: $transaction,
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
