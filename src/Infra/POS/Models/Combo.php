<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class Combo extends BaseModel
{
    protected $table = 'pos_combos';

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function items()
    {
        return $this->belongsToMany(MenuItem::class, 'pos_combo_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
