<?php

namespace Infra\SelfOrder\Models;

use Infra\POS\Models\Combo;
use Infra\POS\Models\MenuItem;
use Infra\Shared\Models\BaseModel;

class SelfOrderItem extends BaseModel
{
    protected $table = 'self_order_items';

    protected $fillable = [
        'self_order_id',
        'menu_item_id',
        'combo_id',
        'item_name',
        'qty',
        'price',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'integer',
        'line_total' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(SelfOrder::class, 'self_order_id');
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class, 'combo_id');
    }
}
