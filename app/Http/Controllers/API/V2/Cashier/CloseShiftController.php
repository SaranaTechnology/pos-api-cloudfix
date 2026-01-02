<?php

namespace App\Http\Controllers\API\V2\Cashier;

use Domain\Cashier\Actions\CloseShiftAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;
use Infra\Shared\Enums\HttpStatus;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CloseShiftController extends BaseController
{
    public function __invoke(Request $request)
    {
        try {
            $request->validate([
                'closing_cash' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            $staff = $request->user('staff');

            $shift = CloseShiftAction::resolve()->execute($staff, $request->only([
                'closing_cash',
                'notes',
            ]));

            return $this->resolveForSuccessResponseWith(
                message: 'Shift berhasil ditutup',
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
