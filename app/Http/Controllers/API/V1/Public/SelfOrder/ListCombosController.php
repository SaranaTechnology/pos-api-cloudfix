<?php

namespace App\Http\Controllers\API\V1\Public\SelfOrder;

use Domain\SelfOrder\Actions\ListPublicCombosAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class ListCombosController extends BaseController
{
    public function __invoke(Request $request, ListPublicCombosAction $action)
    {
        $data = $action->execute($request->all());

        return $this->success($data, 'Combo berhasil diambil');
    }
}
