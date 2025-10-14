<?php

namespace App\Http\Controllers\API\V1\POS\Menu;

use Domain\POS\Actions\Menu\CreateMenuItemAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateMenuController extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|integer|min:0',
                'product_id' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'loyalty_points' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
            ]);

            $menuItem = CreateMenuItemAction::resolve()->execute($request->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Menu POS berhasil dibuat',
                data: $menuItem,
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
