<?php
namespace App\Http\Controllers\API\V1\Setting\Subsribed;

use Illuminate\Http\Request;
use Domain\Settings\Actions\Subsriptions\GetSubsriptionAction;
use Infra\Shared\Controllers\BaseController;

class IndexSubsriptionController extends BaseController{
    public function __invoke(Request $req)
    {
        try {
            $data = GetSubsriptionAction::resolve()->execute($req->query());
            return $data;
        } catch (\Throwable $th) {
            return $this->resolveForFailedResponseWith(message: $th->getMessage());
        }
    }
}