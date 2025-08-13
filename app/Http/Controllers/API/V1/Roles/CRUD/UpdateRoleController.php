<?php

namespace App\Http\Controllers\API\V1\Roles\CRUD;

use Domain\Roles\Actions\UpdateRolesAction;
use Illuminate\Http\Request;
use Infra\Roles\Models\Roles;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class UpdateRoleController extends BaseController
{
    public function __invoke(Request $req, Roles $role)
    {

        try {
            $data = UpdateRolesAction::resolve()->execute($req->all(), $role);

            return $this->resolveForSuccessResponseWith(
                message: 'Update success',
                data: $data
            );
        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage(),
                status: HttpStatus::InternalError
            );
        }

    }
}
