<?php

namespace App\Http\Controllers\API\V1\Public\SelfOrder;

use Domain\SelfOrder\Actions\ShowSelfOrderStatusAction;
use Infra\Shared\Controllers\BaseController;

class ShowOrderStatusController extends BaseController
{
    public function __invoke(string $orderNo, ShowSelfOrderStatusAction $action)
    {
        $data = $action->execute($orderNo);

        return $this->success($data, 'Status order berhasil diambil');
    }
}
