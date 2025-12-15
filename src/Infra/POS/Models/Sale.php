<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class Sale extends BaseModel
{
    protected $table = 'pos_sales';

    protected $fillable = [
        'invoice_no',
        'customer_id',
        'cashier_id',
        'sold_at',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'subtotal' => 'integer',
        'discount' => 'integer',
        'tax' => 'integer',
        'total' => 'integer',
    ];

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

    public function getTotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}
