<?php

namespace App\Http\Controllers\API\V1\POS\Menu;

use Domain\POS\Actions\Menu\DeleteMenuItemAction;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class DeleteMenuController extends BaseController
{
    public function __invoke(MenuItem $menuItem)
    {
        try {
            DeleteMenuItemAction::resolve()->execute($menuItem);

            return $this->resolveForSuccessResponseWith(
                message: 'Menu POS berhasil dihapus',
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
