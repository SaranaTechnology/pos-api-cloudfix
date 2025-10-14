<?php

namespace App\Http\Controllers\API\V1\POS\Combo;

use Domain\POS\Actions\Combo\CreateComboAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateComboController extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|integer|min:0',
                'is_active' => 'nullable|boolean',
                'loyalty_points' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
                'items' => 'required|array|min:1',
                'items.*.menu_item_id' => 'required|integer|distinct',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            $combo = CreateComboAction::resolve()->execute($request->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Combo POS berhasil dibuat',
                data: $combo,
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
