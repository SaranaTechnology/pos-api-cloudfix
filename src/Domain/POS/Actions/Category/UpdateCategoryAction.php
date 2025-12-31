<?php

namespace Domain\POS\Actions\Category;

use Illuminate\Support\Str;
use Infra\POS\Models\Category;

class UpdateCategoryAction
{
    public function execute(Category $category, array $data): Category
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return $category->fresh();
    }
}
