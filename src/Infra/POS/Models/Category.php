<?php

namespace Infra\POS\Models;

use Infra\Shared\Models\BaseModel;

class Category extends BaseModel
{
    protected $table = 'pos_categories';

    protected $casts = [
        'is_active' => 'bool',
        'sort_order' => 'integer',
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }

    public function activeMenuItems()
    {
        return $this->hasMany(MenuItem::class, 'category_id')->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
