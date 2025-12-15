<?php

namespace Infra\SelfOrder\Models;

use Infra\POS\Models\Customer;
use Infra\SelfOrder\Enums\SelfOrderStatus;
use Infra\Shared\Models\BaseModel;

class SelfOrder extends BaseModel
{
    protected $table = 'self_orders';

    protected $fillable = [
        'order_no',
        'customer_name',
        'customer_phone',
        'table_no',
        'customer_id',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
        'pos_sale_id',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'tax' => 'integer',
        'total' => 'integer',
        'status' => SelfOrderStatus::class,
    ];

    public function items()
    {
        return $this->hasMany(SelfOrderItem::class, 'self_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getTotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public static function generateOrderNo(): string
    {
        $prefix = 'SO';
        $date = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -4));

        return "{$prefix}{$date}{$random}";
    }
}
