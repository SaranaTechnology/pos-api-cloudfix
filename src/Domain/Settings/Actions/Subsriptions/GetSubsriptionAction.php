<?php
namespace Domain\Settings\Actions\Subsriptions;

use Infra\Shared\Foundations\Action;
use Infra\Shared\Services\InternalSaranaServices;

class GetSubsriptionAction extends Action{
    public function execute($query){
        $sarana=new InternalSaranaServices();
        return json_decode($sarana->getSubsription($query),true);
    }
}