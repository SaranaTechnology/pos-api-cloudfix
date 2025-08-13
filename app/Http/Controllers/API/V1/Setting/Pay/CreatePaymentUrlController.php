<?php
namespace App\Http\Controllers\API\V1\Setting\Pay;

use Domain\Settings\Actions\Pay\CreateTransactionAction;
use Infra\Shared\Controllers\BaseController;

class CreatePaymentUrlController extends BaseController
{
    public function __invoke($plan_id)
    {
        try {

            $data = CreateTransactionAction::resolve()->execute($plan_id);
            return $this->resolveForSuccessResponseWith(
                message: 'URL berhasil dibuat',
                data: $data
            );

        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(message: $th->getMessage());
        }
    }
}