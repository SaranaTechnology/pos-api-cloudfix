<?php

namespace Domain\POS\Actions\Category;

use Infra\POS\Models\Category;

class ShowCategoryAction
{
    public function execute(int $id): Category
    {
        return Category::with('menuItems')->withCount('activeMenuItems as items_count')->findOrFail($id);
    }
}
