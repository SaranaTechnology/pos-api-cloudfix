<?php

namespace App\Http\Controllers\API\V1\User\CRUD;

use Domain\User\Actions\CRUD\DetailsUserAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Infra\User\Models\User;

class DetailUserController extends BaseController
{
    public function __invoke(User $user, Request $req)
    {
        try {
            $data = DetailsUserAction::resolve()->execute($req->query(), $user);

            return $this->resolveForSuccessResponseWith(
                message: 'Get User Data',
                data: $data
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
