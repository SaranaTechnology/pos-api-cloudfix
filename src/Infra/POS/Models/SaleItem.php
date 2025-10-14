<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class SaleItem extends BaseModel
{
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'pos_sale_id');
    }
}

