<?php

namespace App\Http\Controllers\API\V1\User\CRUD;

use Domain\User\Actions\CRUD\IndexUserAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;

class IndexUserController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {
            $data = IndexUserAction::resolve()->execute($req->query());

            if ($req->has('total_only') && $req->total_only === 'true') {
                return $this->resolveForSuccessResponseWith(
                    message: 'data roles',
                    data: $data
                );
            }

            return $this->resolveForSuccessResponseWithPage(
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
