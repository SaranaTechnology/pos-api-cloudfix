<?php

namespace App\Http\Controllers\API\V1\POS\Menu;

use Domain\POS\Actions\Menu\UpdateMenuItemAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UpdateMenuController extends BaseController
{
    public function __invoke(Request $request, MenuItem $menuItem)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'sku' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|integer|min:0',
                'product_id' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
                'loyalty_points' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
            ]);

            $menuItem = UpdateMenuItemAction::resolve()->execute($menuItem, $request->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Menu POS berhasil diperbarui',
                data: $menuItem,
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
