<?php
namespace Domain\Settings\Actions\Ipaymu;

use Infra\Shared\Foundations\Action;
use Infra\Shared\Services\XenditServices;

class CreatePaymentUrlAction extends Action{

    public function execute($data){
        $datanew['plan_id']=$data['id'];
        $datanew['price']=$data['harga'];
        $datanew['name']=$data['name'];
        $xendit = new XenditServices();
        return $xendit->createPaymentUrl($datanew);
    }
}       