<?php

namespace App\Http\Controllers\API\V1\User\Auth;

use Domain\User\Actions\Auth\LoginAuthAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoginController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {
            $data = LoginAuthAction::resolve()->execute($req->all());

            return $this->resolveForSuccessResponseWith(
                message: 'Login successful',
                data: $data,
                status: HttpStatus::Ok
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
