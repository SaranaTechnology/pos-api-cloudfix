<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class LoyaltyTransaction extends BaseModel
{
    protected $table = 'pos_loyalty_transactions';

    protected $casts = [
        'points' => 'int',
        'balance_after' => 'int',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
