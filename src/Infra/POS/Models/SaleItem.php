<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class SaleItem extends BaseModel
{
    protected $table = 'pos_sale_items';

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'pos_sale_id');
    }
}

