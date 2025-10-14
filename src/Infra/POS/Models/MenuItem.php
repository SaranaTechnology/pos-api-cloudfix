<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class MenuItem extends BaseModel
{
    protected $table = 'pos_menu_items';

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'pos_combo_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
