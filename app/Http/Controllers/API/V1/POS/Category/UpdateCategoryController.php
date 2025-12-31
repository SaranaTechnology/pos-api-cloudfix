<?php

namespace App\Http\Controllers\API\V1\POS\Category;

use Domain\POS\Actions\Category\UpdateCategoryAction;
use Illuminate\Http\Request;
use Infra\POS\Models\Category;
use Infra\Shared\Controllers\BaseController;

class UpdateCategoryController extends BaseController
{
    public function __invoke(Request $request, Category $category, UpdateCategoryAction $action)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'slug' => 'sometimes|string|max:100|unique:pos_categories,slug,' . $category->id,
            'description' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $category = $action->execute($category, $validated);

        return $this->resolveForSuccessResponseWith('Category updated successfully', $category);
    }
}
