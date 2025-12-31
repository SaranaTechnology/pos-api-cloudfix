<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class MenuItem extends BaseModel
{
    protected $table = 'pos_menu_items';

    protected $casts = [
        'is_active' => 'bool',
        'metadata' => 'array',
        'price' => 'decimal:2',
        'loyalty_points' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'pos_combo_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
