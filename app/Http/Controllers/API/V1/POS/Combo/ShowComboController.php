<?php

namespace App\Http\Controllers\API\V1\POS\Combo;

use Domain\POS\Actions\Combo\ShowComboAction;
use Infra\POS\Models\Combo;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class ShowComboController extends BaseController
{
    public function __invoke(Combo $combo)
    {
        try {
            $combo = ShowComboAction::resolve()->execute($combo);

            return $this->resolveForSuccessResponseWith(
                message: 'Detail combo POS berhasil diambil',
                data: $combo,
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
