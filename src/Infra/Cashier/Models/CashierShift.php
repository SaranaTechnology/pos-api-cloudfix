<?php

namespace Infra\Cashier\Models;

use Infra\Shared\Models\BaseModel;
use Infra\Staff\Models\Staff;

class CashierShift extends BaseModel
{
    protected $table = 'cashier_shifts';

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash_sales' => 'decimal:2',
        'total_non_cash_sales' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }
}
