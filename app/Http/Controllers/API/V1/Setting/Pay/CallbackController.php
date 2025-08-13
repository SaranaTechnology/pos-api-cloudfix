<?php
namespace App\Http\Controllers\API\V1\Setting\Pay;

use Domain\Settings\Actions\Ipaymu\CallbackAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class CallbackController extends BaseController{
    public function __invoke(Request  $req)
    {
        try {

            $action = CallbackAction::resolve()->execute($req->all());
            return $this->resolveForSuccessResponseWith(
                message: 'callback berhasil',
                data: $action
            );

        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(message: $th->getMessage());
        }
    }
}