<?php

namespace App\Http\Controllers\API\V2\Cashier;

use Domain\Cashier\Actions\OpenShiftAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OpenShiftController extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'opening_cash' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $staff = $request->user('staff');

            $shift = OpenShiftAction::resolve()->execute($staff, $request->only([
                'opening_cash',
                'notes',
            ]));

            return $this->resolveForSuccessResponseWith(
                message: 'Shift berhasil dibuka',
                data: $shift
            );
        } catch (BadRequestException $e) {
            return $this->resolveForFailedResponseWith(
                message: $e->getMessage(),
                status: HttpStatus::BadRequest
            );
        } catch (\Throwable $e) {
            return $this->resolveForFailedResponseWith(
                message: $e->getMessage()
            );
        }
    }
}
