<?php

namespace App\Http\Controllers\API\V1\Kitchen;

use Domain\SelfOrder\Actions\IndexKitchenOrdersAction;
use Illuminate\Http\Request;
use Infra\Shared\Controllers\BaseController;

class IndexKitchenOrdersController extends BaseController
{
    public function __invoke(Request $request, IndexKitchenOrdersAction $action)
    {
        $filters = $request->only(['statuses', 'table_no', 'date', 'per_page']);

        if ($request->has('status')) {
            $filters['statuses'] = explode(',', $request->get('status'));
        }

        $orders = $action->execute($filters);

        return $this->resolveForSuccessResponseWithPage('Success', $orders);
    }
}
