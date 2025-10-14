<?php

namespace App\Http\Controllers\API\V1\POS\Combo;

use Domain\POS\Actions\Combo\UpdateComboAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\POS\Models\Combo;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdateComboController extends BaseController
{
    public function __invoke(Request $request, Combo $combo)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
                'loyalty_points' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
                'items' => 'nullable|array|min:1',
                'items.*.menu_item_id' => 'required_with:items|integer|distinct',
                'items.*.quantity' => 'required_with:items|integer|min:1',
            ]);

            $combo = UpdateComboAction::resolve()->execute($combo, $request->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Combo POS berhasil diperbarui',
                data: $combo,
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
