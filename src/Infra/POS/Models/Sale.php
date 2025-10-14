<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class Sale extends BaseModel
{

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'pos_sale_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'pos_sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
