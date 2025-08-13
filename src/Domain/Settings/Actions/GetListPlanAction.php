<?php
namespace Domain\Settings\Actions;

use Infra\Shared\Foundations\Action;
use Infra\Shared\Services\InternalSaranaServices;

class GetListPlanAction extends Action
{
    public function execute($query)
    {
        $internal = new InternalSaranaServices();
        return $internal->getListPlan($query);
    }
}