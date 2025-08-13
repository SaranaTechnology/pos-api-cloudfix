<?php
namespace Domain\Settings\Actions\Pay;

use Domain\Settings\Actions\Ipaymu\CreatePaymentUrlAction;
use Infra\Shared\Foundations\Action;
use Infra\Shared\Services\InternalSaranaServices;
use Infra\Transaction\Models\Transaction;

class CreateTransactionAction extends Action{
    public function execute($plan_id){
        $data=$this->getPlanData($plan_id);
        $client=$this->getClientData();
        $client=$client['data'];
        
        $ipaymuRequest=CreatePaymentUrlAction::resolve()->execute($data['data']);
        Transaction::create([
            'client_id'=>$client['id'],
            'plan_id'=>$data['data']['id'],
            'status'=>'pending',
            'buyer_payment'=>$data['data']['harga'],
            'net_payment'=>0,
            'transaction_fee'=>0,
            'plan_name'=>$data['data']['name'],
            'transaction_sid'=>$ipaymuRequest['Data']['SessionID']
        ]);
        return $ipaymuRequest['Data'];
    }
    protected function getClientData(){
        $sarana=new InternalSaranaServices();
        return json_decode($sarana->getClientData(),true);
        
    }
    protected function getPlanData($id){
        $sarana=new InternalSaranaServices();
        return json_decode($sarana->getPlanDataDetail($id),true);
    }
}