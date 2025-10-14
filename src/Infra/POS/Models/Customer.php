<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class Customer extends BaseModel
{
    protected $table = 'pos_customers';

    protected $casts = [
        'metadata' => 'array',
    ];

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
