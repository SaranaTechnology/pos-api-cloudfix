<?php

namespace App\Http\Controllers\API\V1\Public\SelfOrder;

use Domain\SelfOrder\Actions\ListPublicMenuAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class ListMenuController extends BaseController
{
    public function __invoke(Request $request, ListPublicMenuAction $action)
    {
        $filters = $request->only(['category', 'category_id', 'search', 'per_page']);

        $data = $action->execute($filters);

        // If paginated
        if (method_exists($data, 'items')) {
            return $this->resolveForSuccessResponseWithPage('Menu berhasil diambil', $data);
        }

        return $this->resolveForSuccessResponseWith('Menu berhasil diambil', $data);
    }
}
