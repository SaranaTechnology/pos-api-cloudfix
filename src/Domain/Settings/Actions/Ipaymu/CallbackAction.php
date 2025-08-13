<?php
namespace Domain\Settings\Actions\Ipaymu;

use Carbon\Carbon;
use Infra\Shared\Foundations\Action;
use Infra\Shared\Services\InternalSaranaServices;
use Infra\Transaction\Models\Transaction;

class CallbackAction extends Action{
    public function execute($data){
        $transaction = Transaction::where('transaction_sid', $data['sid'])->first();
        $transaction->payment_at = Carbon::now();
        $transaction->transaction_id_ipaymu = $data['trx_id'];
        $transaction->payment_method = $data['via'];
        $transaction->status = $data['status'];
        $transaction->save();
        if($transaction->status_code==1){
            $this->generatedPlan($transaction);
        }
        if($transaction->status_code==-2){
            $transaction->status='expired';
            $transaction->save();
        }
        $this->buat_pencatatan($transaction, $data);
        return $transaction;
        
    }
    protected function generatedPlan($transaction){
        $sarana=new InternalSaranaServices();
        $sarana->generatedPlan($transaction);
    }

    protected function buat_pencatatan(Transaction $transaction, $ipaymu)
    {
        $data['transaction_log']['ipaymu'] = $ipaymu;
        $data['transaction_log']['transaction'] = $transaction->toArray();
        $data['total'] = $transaction->buyer_payment;
        $data['net_payment'] = $transaction->net_payment;
        $data['status'] = $transaction->status;
        $data['transaction_id'] = $ipaymu['trx_id'];
        $sarana = new InternalSaranaServices();
        $sarana->sendLogToTransaction($data);
    }
}