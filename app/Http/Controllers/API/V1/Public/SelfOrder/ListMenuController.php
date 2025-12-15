<?php

namespace App\Http\Controllers\API\V1\Public\SelfOrder;

use Domain\SelfOrder\Actions\ListPublicMenuAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class ListMenuController extends BaseController
{
    public function __invoke(Request $request, ListPublicMenuAction $action)
    {
        $data = $action->execute($request->only(['category', 'search']));

        return $this->success($data, 'Menu berhasil diambil');
    }
}
