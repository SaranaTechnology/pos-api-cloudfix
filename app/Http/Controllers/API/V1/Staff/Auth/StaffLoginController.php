<?php

namespace App\Http\Controllers\API\V1\Staff\Auth;

use Domain\Staff\Actions\Auth\StaffLoginAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class StaffLoginController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {
            $req->validate([
                'nip' => 'required|string',
                'password' => 'required|string',
            ]);

            $data = StaffLoginAction::resolve()->execute($req->only(['nip', 'password']));

            return $this->resolveForSuccessResponseWith(
                message: 'Login berhasil',
                data: $data
            );
        } catch (BadRequestException $th) {
            return $this->resolveForFailedResponseWith(
                message: $th->getMessage(),
                status: HttpStatus::BadRequest
            );
        } catch (\Throwable $th) {
            Log::error('Staff Login Error: ' . $th->getMessage());

            return $this->resolveForFailedResponseWith(
                message: $th->getMessage()
            );
        }
    }
}
