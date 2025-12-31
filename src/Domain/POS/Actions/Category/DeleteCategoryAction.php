<?php

namespace Domain\POS\Actions\Category;

use Infra\POS\Models\Category;

class DeleteCategoryAction
{
    public function execute(Category $category): bool
    {
        return $category->delete();
    }
}
