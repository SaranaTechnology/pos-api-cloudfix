<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class SaleItem extends BaseModel
{
    protected $table = 'pos_sale_items';

    protected $fillable = [
        'pos_sale_id',
        'product_id',
        'menu_item_id',
        'product_name',
        'qty',
        'price',
        'discount',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'integer',
        'discount' => 'integer',
        'line_total' => 'integer',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'pos_sale_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}

