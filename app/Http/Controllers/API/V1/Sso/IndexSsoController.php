<?php

namespace App\Http\Controllers\API\V1\Sso;

use Domain\DataSso\Actions\IndexSsoAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class IndexSsoController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {
            $data = IndexSsoAction::resolve()->execute($req->query());

            return $this->resolveForSuccessResponseWithPage(
                message: 'LIST of SSO',
                data: $data
            );
        } catch (BadRequestException $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage(),
                status: HttpStatus::BadRequest
            );
        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage(),
                status: HttpStatus::InternalError
            );
        }
    }
}
