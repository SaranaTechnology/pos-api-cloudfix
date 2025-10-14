<?php

namespace App\Http\Controllers\API\V1\POS\Combo;

use Domain\POS\Actions\Combo\DeleteComboAction;
use Infra\POS\Models\Combo;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class DeleteComboController extends BaseController
{
    public function __invoke(Combo $combo)
    {
        try {
            DeleteComboAction::resolve()->execute($combo);

            return $this->resolveForSuccessResponseWith(
                message: 'Combo POS berhasil dihapus',
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
