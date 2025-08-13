<?php

namespace App\Http\Controllers\API\V1\User\Auth;

use Domain\User\Actions\CRUD\UpdateUserAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class EditProfileController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {

            $data = UpdateUserAction::resolve()->execute($req->all(), $req->user());

            return $this->resolveForSuccessResponseWith(
                message: 'Profile updated successfully',
                data: $data
            );
        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage()
            );
        }
    }
}
