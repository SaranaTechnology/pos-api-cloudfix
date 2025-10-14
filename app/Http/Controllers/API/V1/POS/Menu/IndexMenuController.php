<?php

namespace App\Http\Controllers\API\V1\POS\Menu;

use Domain\POS\Actions\Menu\ListMenuItemsAction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class IndexMenuController extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string',
                'is_active' => 'nullable|boolean',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $paginator = ListMenuItemsAction::resolve()->execute($request->only([
                'search',
                'is_active',
                'per_page',
            ]));

            return $this->resolveForSuccessResponseWithPage(
                message: 'Daftar menu POS berhasil diambil',
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
