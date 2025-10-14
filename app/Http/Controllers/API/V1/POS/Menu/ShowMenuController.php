<?php

namespace App\Http\Controllers\API\V1\POS\Menu;

use Domain\POS\Actions\Menu\ShowMenuItemAction;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class ShowMenuController extends BaseController
{
    public function __invoke(MenuItem $menuItem)
    {
        try {
            $menuItem = ShowMenuItemAction::resolve()->execute($menuItem);

            return $this->resolveForSuccessResponseWith(
                message: 'Detail menu POS berhasil diambil',
                data: $menuItem,
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
