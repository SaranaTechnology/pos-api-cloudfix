<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class Payment extends BaseModel
{
    protected $table = 'pos_payments';

    protected $fillable = [
        'pos_sale_id',
        'method',
        'amount',
        'change',
        'reference',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'integer',
        'change' => 'integer',
        'metadata' => 'array',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'pos_sale_id');
    }
}
