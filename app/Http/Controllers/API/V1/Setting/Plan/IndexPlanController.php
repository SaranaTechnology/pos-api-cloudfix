<?php

namespace App\Http\Controllers\API\V1\Setting\Plan;

use Domain\Settings\Actions\GetListPlanAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class IndexPlanController extends BaseController
{
    public function __invoke(Request $req)
    {
        try {

            $action = GetListPlanAction::resolve()->execute($req->query());
            return $action;

        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(message: $th->getMessage());
        }
    }
}
