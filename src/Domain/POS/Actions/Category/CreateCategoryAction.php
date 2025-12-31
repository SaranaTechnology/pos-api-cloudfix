<?php

namespace Domain\POS\Actions\Category;

use Illuminate\Support\Str;
use Infra\POS\Models\Category;

class CreateCategoryAction
{
    public function execute(array $data): Category
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return Category::create($data);
    }
}
